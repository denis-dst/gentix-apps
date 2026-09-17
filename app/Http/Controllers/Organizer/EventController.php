<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\WristbandTemplate;
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
        $this->filterEmptyFileUploads($request);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'google_maps_url' => 'nullable|url|max:2048',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'background_image' => 'nullable|image|max:10240',
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
            'wristband_mode' => 'nullable|in:default,custom',
            'wristband_custom_background' => 'nullable|image|max:10240',
            'wristband_custom_background_base64' => 'nullable|string',
            'wristband_columns_json' => 'nullable|string',
            'proof_ig_required' => 'nullable|boolean',
            'proof_review_required' => 'nullable|boolean',
            'registration_proofs_json' => 'nullable|string',
            'evoucher_page_mode' => 'nullable|in:1_page,multi_page',
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
            $validated['wristband_mode'],
            $validated['wristband_custom_background'],
            $validated['wristband_custom_background_base64'],
            $validated['wristband_columns_json'],
            $validated['proof_ig_required'],
            $validated['proof_review_required']
        );
        
        if (empty($validated['security_code'])) {
            $validated['security_code'] = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        if ($request->hasFile('background_image')) {
            $validated['background_image'] = \App\Services\ImageOptimizerService::uploadAndOptimize($request->file('background_image'), 'events/backgrounds', 1200, 80);
        }

        $event = Event::create($validated);
        $this->syncWristbandTemplate($request, $event);

        return redirect()->route('organizer.events.edit', $event)->with('success', 'Event created. Now add ticket categories.');
    }

    public function edit(Event $event)
    {
        $this->authorizeTenant($event);
        
        $event->load(['ticketCategories', 'wristbandTemplate']);
        return view('organizer.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeTenant($event);

        // Only remove empty file inputs when no file was chosen
        $this->filterEmptyFileUploads($request);

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
            'background_image' => 'nullable|image|max:10240',
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
            'wristband_league_logo' => 'nullable|image|max:5120',
            'wristband_home_club_logo' => 'nullable|image|max:5120',
            'wristband_away_club_logo' => 'nullable|image|max:5120',
            'wristband_sponsor_logos' => 'nullable|array',
            'wristband_sponsor_logos.*' => 'nullable|image|max:5120',
            'wristband_mode' => 'nullable|in:default,custom',
            'wristband_custom_background' => 'nullable|image|max:10240',
            'wristband_custom_background_base64' => 'nullable|string',
            'wristband_columns_json' => 'nullable|string',
            'proof_ig_required' => 'nullable|boolean',
            'proof_review_required' => 'nullable|boolean',
            'registration_proofs_json' => 'nullable|string',
            'evoucher_page_mode' => 'nullable|in:1_page,multi_page',
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
            $validated['wristband_sponsor_logos'],
            $validated['wristband_mode'],
            $validated['wristband_custom_background'],
            $validated['wristband_custom_background_base64'],
            $validated['wristband_columns_json']
        );

        if ($request->hasFile('background_image')) {
            if ($event->background_image) Storage::disk('public')->delete($event->background_image);
            $validated['background_image'] = \App\Services\ImageOptimizerService::uploadAndOptimize($request->file('background_image'), 'events/backgrounds', 1200, 80);
        }

        $event->update($validated);
        $this->syncWristbandTemplate($request, $event);

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
        $meta['evoucher_page_mode'] = $request->input('evoucher_page_mode', $meta['evoucher_page_mode'] ?? '1_page');

        foreach ([
            'wristband_league_logo',
            'wristband_home_club_logo',
            'wristband_away_club_logo',
        ] as $input) {
            if ($request->hasFile($input)) {
                if (!empty($meta[$input])) {
                    Storage::disk('public')->delete($meta[$input]);
                }
                $meta[$input] = \App\Services\ImageOptimizerService::uploadAndOptimize($request->file($input), 'wristbands/logos', 300, 85);
            }
        }

        if ($request->hasFile('wristband_sponsor_logos')) {
            foreach (($meta['wristband_sponsor_logos'] ?? []) as $logo) {
                Storage::disk('public')->delete($logo);
            }

            $meta['wristband_sponsor_logos'] = collect($request->file('wristband_sponsor_logos'))
                ->filter()
                ->map(fn ($file) => \App\Services\ImageOptimizerService::uploadAndOptimize($file, 'wristbands/sponsors', 300, 85))
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

    /**
     * Duplikasi / Copy Event beserta seluruh kategori tiket dan gate
     * Kecuali kode verifikasi event (dibuatkan baru secara acak)
     */
    public function duplicate(Event $event)
    {
        $this->authorizeTenant($event);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // 1. Replicate Event
            $newEvent = $event->replicate([
                'slug',
                'security_code',
                'status',
            ]);

            $newEvent->name = $event->name . ' (Salinan)';
            $newEvent->slug = \Illuminate\Support\Str::slug($newEvent->name) . '-' . rand(1000, 9999);
            $newEvent->status = 'draft';
            // Generate kode verifikasi event baru (6 digit angka acak)
            $newEvent->security_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $newEvent->created_at = now();
            $newEvent->updated_at = now();
            $newEvent->save();

            // 2. Replicate Ticket Categories
            $categoryMap = [];
            $originalCategories = TicketCategory::where('event_id', $event->id)->get();
            foreach ($originalCategories as $cat) {
                $newCat = $cat->replicate([
                    'event_id',
                    'sold_count',
                ]);
                $newCat->event_id = $newEvent->id;
                $newCat->sold_count = 0;
                $newCat->created_at = now();
                $newCat->updated_at = now();
                $newCat->save();

                $categoryMap[$cat->id] = $newCat->id;
            }

            // 3. Replicate Gates and sync category associations
            $originalGates = \App\Models\Gate::where('event_id', $event->id)->with('ticketCategories')->get();
            foreach ($originalGates as $gate) {
                $newGate = $gate->replicate([
                    'event_id',
                ]);
                $newGate->event_id = $newEvent->id;
                $newGate->created_at = now();
                $newGate->updated_at = now();
                $newGate->save();

                $mappedCategoryIds = [];
                foreach ($gate->ticketCategories as $gateCat) {
                    if (isset($categoryMap[$gateCat->id])) {
                        $mappedCategoryIds[] = $categoryMap[$gateCat->id];
                    }
                }
                if (!empty($mappedCategoryIds)) {
                    $newGate->ticketCategories()->sync($mappedCategoryIds);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('organizer.events.edit', $newEvent)
                ->with('success', 'Event berhasil diduplikasi beserta seluruh kategori tiket dan gerbang gate! Kode verifikasi baru telah di-generate.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menduplikasi event: ' . $e->getMessage());
        }
    }

    /**
     * Filter out empty or invalid file uploads from both Symfony FileBag and Laravel convertedFiles.
     */
    private function filterEmptyFileUploads(Request $request): void
    {
        $singleInputs = [
            'background_image',
            'wristband_league_logo',
            'wristband_home_club_logo',
            'wristband_away_club_logo',
            'wristband_custom_background',
        ];

        $ref = new \ReflectionClass($request);
        $convertedProp = $ref->hasProperty('convertedFiles') ? $ref->getProperty('convertedFiles') : null;
        if ($convertedProp) {
            $convertedProp->setAccessible(true);
        }

        foreach ($singleInputs as $input) {
            if ($request->files->has($input)) {
                $file = $request->files->get($input);
                if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                    $request->files->remove($input);
                    if ($convertedProp) {
                        $converted = $convertedProp->getValue($request);
                        if (is_array($converted)) {
                            unset($converted[$input]);
                            $convertedProp->setValue($request, $converted);
                        }
                    }
                } elseif (!$file->isValid() && $request->filled($input . '_base64')) {
                    // Remove failed upload if base64 payload is available so validation passes
                    $request->files->remove($input);
                    if ($convertedProp) {
                        $converted = $convertedProp->getValue($request);
                        if (is_array($converted)) {
                            unset($converted[$input]);
                            $convertedProp->setValue($request, $converted);
                        }
                    }
                }
            }
        }

        if ($request->files->has('wristband_sponsor_logos')) {
            $files = $request->files->get('wristband_sponsor_logos');
            if (is_array($files)) {
                $filtered = array_filter($files, function ($file) {
                    return $file && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE;
                });
                if (empty($filtered)) {
                    $request->files->remove('wristband_sponsor_logos');
                    if ($convertedProp) {
                        $converted = $convertedProp->getValue($request);
                        if (is_array($converted)) {
                            unset($converted['wristband_sponsor_logos']);
                            $convertedProp->setValue($request, $converted);
                        }
                    }
                } else {
                    $request->files->set('wristband_sponsor_logos', array_values($filtered));
                    if ($convertedProp) {
                        $converted = $convertedProp->getValue($request);
                        if (is_array($converted)) {
                            $converted['wristband_sponsor_logos'] = array_values($filtered);
                            $convertedProp->setValue($request, $converted);
                        }
                    }
                }
            }
        }
    }

    private function syncWristbandTemplate(Request $request, Event $event): WristbandTemplate
    {
        $template = WristbandTemplate::where('event_id', $event->id)->whereNull('ticket_category_id')->latest()->first()
            ?: WristbandTemplate::firstOrNew(['event_id' => $event->id]);
        $template->tenant_id = $event->tenant_id;
        $template->name = 'Wristband ' . $event->name;
        $template->mode = $request->input('wristband_mode', 'default') === 'custom' ? 'custom' : 'default';

        $base64Input = $request->input('wristband_custom_background_base64');
        $hasBase64Bg = !empty($base64Input) && str_starts_with($base64Input, 'data:image/');
        $hasFileBg = $request->hasFile('wristband_custom_background');

        if ($request->boolean('wristband_remove_background') && !$hasFileBg && !$hasBase64Bg) {
            if ($template->background_image) {
                Storage::disk('public')->delete($template->background_image);
                $template->background_image = null;
            }
        } elseif ($hasBase64Bg) {
            if ($template->background_image) {
                Storage::disk('public')->delete($template->background_image);
            }
            $data = substr($base64Input, strpos($base64Input, ',') + 1);
            $decoded = base64_decode($data);
            if ($decoded !== false) {
                $ext = 'webp';
                if (str_contains($base64Input, 'image/png')) {
                    $ext = 'png';
                } elseif (str_contains($base64Input, 'image/jpeg') || str_contains($base64Input, 'image/jpg')) {
                    $ext = 'jpg';
                }
                $filename = 'wristbands/backgrounds/' . \Illuminate\Support\Str::random(40) . '.' . $ext;
                Storage::disk('public')->put($filename, $decoded);
                $template->background_image = $filename;
            }
        } elseif ($hasFileBg) {
            if ($template->background_image) {
                Storage::disk('public')->delete($template->background_image);
            }
            $template->background_image = \App\Services\ImageOptimizerService::uploadAndOptimize(
                $request->file('wristband_custom_background'),
                'wristbands/backgrounds',
                2560,
                90
            );
        }

        if ($request->filled('wristband_columns_json')) {
            $decoded = json_decode($request->input('wristband_columns_json'), true);
            if (is_array($decoded)) {
                foreach ($decoded as $key => &$col) {
                    if (isset($col['x'])) {
                        $col['x'] = (float) str_replace(',', '.', (string) $col['x']);
                    }
                    if (isset($col['y'])) {
                        $col['y'] = (float) str_replace(',', '.', (string) $col['y']);
                    }
                    if (isset($col['qr_size'])) {
                        $col['qr_size'] = (float) str_replace(',', '.', (string) $col['qr_size']);
                    }
                }
                $template->columns_config = $decoded;
            }
        } elseif (!$template->columns_config) {
            $template->columns_config = WristbandTemplate::getDefaultColumns();
        }

        $template->save();
        return $template;
    }
}
