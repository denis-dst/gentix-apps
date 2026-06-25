<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::where('tenant_id', auth()->user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('organizer.events.index', compact('events'));
    }

    public function create()
    {
        return view('organizer.events.create');
    }

    public function store(Request $request)
    {
        // Filter out empty/invalid file uploads before validation to prevent Laravel from failing on nullable fields
        foreach ([
            'wristband_league_logo',
            'wristband_home_club_logo',
            'wristband_away_club_logo',
        ] as $input) {
            if ($request->files->has($input)) {
                $file = $request->files->get($input);
                if ($file && !$file->isValid()) {
                    $request->files->remove($input);
                }
            }
        }

        if ($request->files->has('wristband_sponsor_logos')) {
            $files = $request->files->get('wristband_sponsor_logos');
            if (is_array($files)) {
                $filtered = array_filter($files, function ($file) {
                    return $file && $file->isValid();
                });
                if (empty($filtered)) {
                    $request->files->remove('wristband_sponsor_logos');
                } else {
                    $request->files->set('wristband_sponsor_logos', $filtered);
                }
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'google_maps_url' => 'nullable|url|max:2048',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'security_code' => 'nullable|string|size:6',
            'is_free' => 'nullable|boolean',
            'max_tickets_per_transaction' => 'nullable|integer|min:1',
            'umroh_question_enabled' => 'nullable|boolean',
            'custom_question_text' => 'nullable|string|max:255',
            'custom_question_type' => 'nullable|in:text,select',
            'custom_question_options' => 'nullable|string',
            'evoucher_info' => 'nullable|string',
            'purchase_flow' => 'required|in:redeem,evoucher,print,both',
            'thermal_paper_width_mm' => 'nullable|integer|min:40|max:120',
            'thermal_paper_height_mm' => 'nullable|integer|min:60|max:300',
            'wristband_league_name' => 'nullable|string|max:255',
            'wristband_league_logo' => 'nullable|image|max:1024',
            'wristband_home_club_logo' => 'nullable|image|max:1024',
            'wristband_away_club_logo' => 'nullable|image|max:1024',
            'wristband_sponsor_logos' => 'nullable|array',
            'wristband_sponsor_logos.*' => 'nullable|image|max:1024',
            'proof_ig_required' => 'nullable|boolean',
            'proof_review_required' => 'nullable|boolean',
            'registration_proofs_json' => 'nullable|string',
        ]);

        $validated['is_free'] = $request->boolean('is_free');
        $validated['max_tickets_per_transaction'] = $request->input('is_free') ? $request->integer('max_tickets_per_transaction', 1) : 1;
        $validated['umroh_question_enabled'] = $request->boolean('umroh_question_enabled');
        $validated['thermal_paper_width_mm'] = $request->integer('thermal_paper_width_mm', 80);
        $validated['thermal_paper_height_mm'] = $request->integer('thermal_paper_height_mm', 160);


        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = 'draft';
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . rand(1000, 9999);
        
        $meta = $this->buildWristbandMeta($request);
        $proofs = $this->parseRegistrationProofs($request);
        $meta['registration_proofs'] = $proofs;
        $meta['proof_ig_required'] = collect($proofs)->first(fn($p) => $p['id'] === 'proof_ig')['is_required'] ?? false;
        $meta['proof_review_required'] = collect($proofs)->first(fn($p) => $p['id'] === 'proof_review')['is_required'] ?? false;
        if ($validated['umroh_question_enabled']) {
            $meta['custom_question_text'] = $request->input('custom_question_text', 'Alumni Grup Keberangkatan Tanggal Berapa?');
            $meta['custom_question_type'] = $request->input('custom_question_type', 'text');
            if ($meta['custom_question_type'] === 'select') {
                $optionsStr = $request->input('custom_question_options', '');
                $optionsArray = collect(explode("\n", $optionsStr))
                    ->map(fn($o) => trim($o))
                    ->filter()
                    ->values()
                    ->all();
                $meta['custom_question_options'] = $optionsArray;
            }
        }
        $validated['meta'] = $meta;
        unset(
            $validated['wristband_league_name'],
            $validated['wristband_league_logo'],
            $validated['wristband_home_club_logo'],
            $validated['wristband_away_club_logo'],
            $validated['wristband_sponsor_logos'],
            $validated['proof_ig_required'],
            $validated['proof_review_required']
        );
        
        if (empty($validated['security_code'])) {
            $validated['security_code'] = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        $event = Event::create($validated);

        return redirect()->route('organizer.events.edit', $event)->with('success', 'Event created. Now add ticket categories.');
    }

    public function edit(Event $event)
    {
        $this->authorizeTenant($event);
        
        $event->load('ticketCategories');
        return view('organizer.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeTenant($event);

        // Filter out empty/invalid file uploads before validation to prevent Laravel from failing on nullable fields
        foreach ([
            'background_image',
            'wristband_league_logo',
            'wristband_home_club_logo',
            'wristband_away_club_logo',
        ] as $input) {
            if ($request->files->has($input)) {
                $file = $request->files->get($input);
                if ($file && !$file->isValid()) {
                    $request->files->remove($input);
                }
            }
        }

        if ($request->files->has('wristband_sponsor_logos')) {
            $files = $request->files->get('wristband_sponsor_logos');
            if (is_array($files)) {
                $filtered = array_filter($files, function ($file) {
                    return $file && $file->isValid();
                });
                if (empty($filtered)) {
                    $request->files->remove('wristband_sponsor_logos');
                } else {
                    $request->files->set('wristband_sponsor_logos', $filtered);
                }
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'google_maps_url' => 'nullable|url|max:2048',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'status' => 'required|in:draft,published,cancelled',
            'background_image' => 'nullable|image|max:2048',
            'security_code' => 'required|string|size:6',
            'is_free' => 'nullable|boolean',
            'max_tickets_per_transaction' => 'nullable|integer|min:1',
            'umroh_question_enabled' => 'nullable|boolean',
            'custom_question_text' => 'nullable|string|max:255',
            'custom_question_type' => 'nullable|in:text,select',
            'custom_question_options' => 'nullable|string',
            'evoucher_info' => 'nullable|string',
            'purchase_flow' => 'required|in:redeem,evoucher,print,both',
            'thermal_paper_width_mm' => 'nullable|integer|min:40|max:120',
            'thermal_paper_height_mm' => 'nullable|integer|min:60|max:300',
            'wristband_league_name' => 'nullable|string|max:255',
            'wristband_league_logo' => 'nullable|image|max:1024',
            'wristband_home_club_logo' => 'nullable|image|max:1024',
            'wristband_away_club_logo' => 'nullable|image|max:1024',
            'wristband_sponsor_logos' => 'nullable|array',
            'wristband_sponsor_logos.*' => 'nullable|image|max:1024',
            'proof_ig_required' => 'nullable|boolean',
            'proof_review_required' => 'nullable|boolean',
            'registration_proofs_json' => 'nullable|string',
        ]);

        $validated['is_free'] = $request->boolean('is_free');
        $validated['max_tickets_per_transaction'] = $request->input('is_free') ? $request->integer('max_tickets_per_transaction', 1) : 1;
        $validated['umroh_question_enabled'] = $request->boolean('umroh_question_enabled');
        $validated['thermal_paper_width_mm'] = $request->integer('thermal_paper_width_mm', 80);
        $validated['thermal_paper_height_mm'] = $request->integer('thermal_paper_height_mm', 160);

        $meta = $this->buildWristbandMeta($request, $event->meta ?? []);
        $proofs = $this->parseRegistrationProofs($request);
        $meta['registration_proofs'] = $proofs;
        $meta['proof_ig_required'] = collect($proofs)->first(fn($p) => $p['id'] === 'proof_ig')['is_required'] ?? false;
        $meta['proof_review_required'] = collect($proofs)->first(fn($p) => $p['id'] === 'proof_review')['is_required'] ?? false;
        if ($validated['umroh_question_enabled']) {
            $meta['custom_question_text'] = $request->input('custom_question_text', 'Alumni Grup Keberangkatan Tanggal Berapa?');
            $meta['custom_question_type'] = $request->input('custom_question_type', 'text');
            if ($meta['custom_question_type'] === 'select') {
                $optionsStr = $request->input('custom_question_options', '');
                $optionsArray = collect(explode("\n", $optionsStr))
                    ->map(fn($o) => trim($o))
                    ->filter()
                    ->values()
                    ->all();
                $meta['custom_question_options'] = $optionsArray;
            } else {
                unset($meta['custom_question_options']);
            }
        } else {
            unset($meta['custom_question_text'], $meta['custom_question_type'], $meta['custom_question_options']);
        }
        $validated['meta'] = $meta;
        unset(
            $validated['wristband_league_name'],
            $validated['wristband_league_logo'],
            $validated['wristband_home_club_logo'],
            $validated['wristband_away_club_logo'],
            $validated['wristband_sponsor_logos']
        );

        if ($request->hasFile('background_image')) {
            if ($event->background_image) Storage::disk('public')->delete($event->background_image);
            $validated['background_image'] = $request->file('background_image')->store('events/backgrounds', 'public');
        }

        $event->update($validated);

        return redirect()->route('organizer.events.edit', $event)->with('success', 'Event updated successfully.');
    }

    private function authorizeTenant(Event $event)
    {
        if ($event->tenant_id !== auth()->user()->tenant_id && !auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Unauthorized access to this event');
        }
    }

    private function buildWristbandMeta(Request $request, array $current = []): array
    {
        $meta = $current;
        $meta['wristband_league_name'] = $request->input('wristband_league_name') ?: ($meta['wristband_league_name'] ?? null);

        foreach ([
            'wristband_league_logo',
            'wristband_home_club_logo',
            'wristband_away_club_logo',
        ] as $input) {
            if ($request->hasFile($input)) {
                if (!empty($meta[$input])) {
                    Storage::disk('public')->delete($meta[$input]);
                }
                $meta[$input] = $request->file($input)->store('wristbands/logos', 'public');
            }
        }

        if ($request->hasFile('wristband_sponsor_logos')) {
            foreach (($meta['wristband_sponsor_logos'] ?? []) as $logo) {
                Storage::disk('public')->delete($logo);
            }

            $meta['wristband_sponsor_logos'] = collect($request->file('wristband_sponsor_logos'))
                ->filter()
                ->map(fn ($file) => $file->store('wristbands/sponsors', 'public'))
                ->values()
                ->all();
        }

        return array_filter($meta, fn ($value) => filled($value));
    }

    /**
     * Parse custom or legacy registration proofs from the request.
     */
    private function parseRegistrationProofs(Request $request): array
    {
        $proofs = [];
        if ($request->filled('registration_proofs_json')) {
            $decoded = json_decode($request->input('registration_proofs_json'), true);
            if (is_array($decoded)) {
                foreach ($decoded as $proof) {
                    if (empty($proof['label'])) {
                        continue;
                    }
                    $proofs[] = [
                        'id' => !empty($proof['id']) ? $proof['id'] : ('proof_' . \Illuminate\Support\Str::random(8)),
                        'label' => $proof['label'],
                        'instruction' => $proof['instruction'] ?? '',
                        'link' => $proof['link'] ?? null,
                        'is_required' => isset($proof['is_required']) ? filter_var($proof['is_required'], FILTER_VALIDATE_BOOLEAN) : false,
                    ];
                }
            }
        } else {
            // Fallback for compatibility/default form submissions
            if ($request->has('proof_ig_required') || $request->has('proof_review_required')) {
                if ($request->boolean('proof_ig_required')) {
                    $proofs[] = [
                        'id' => 'proof_ig',
                        'label' => 'Bukti follow IG',
                        'instruction' => 'Klik untuk follow @batikumrah dan ambil screenshot',
                        'link' => 'https://www.instagram.com/batikumrah?igsh=MTFibTFtOHF3dGp4MQ==',
                        'is_required' => true,
                    ];
                }
                if ($request->boolean('proof_review_required')) {
                    $proofs[] = [
                        'id' => 'proof_review',
                        'label' => 'Bukti Google Review',
                        'instruction' => 'Isi Google Review lalu ambil screenshot',
                        'link' => 'https://bit.ly/googlereviewbatik',
                        'is_required' => true,
                    ];
                }
            }
        }
        return $proofs;
    }
}
