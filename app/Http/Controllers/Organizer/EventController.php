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
            'evoucher_info' => 'nullable|string',
            'wristband_league_name' => 'nullable|string|max:255',
            'wristband_league_logo' => 'nullable|image|max:1024',
            'wristband_home_club_logo' => 'nullable|image|max:1024',
            'wristband_away_club_logo' => 'nullable|image|max:1024',
            'wristband_sponsor_logos' => 'nullable|array',
            'wristband_sponsor_logos.*' => 'nullable|image|max:1024',
        ]);

        $validated['is_free'] = $request->boolean('is_free');
        $validated['max_tickets_per_transaction'] = $request->input('is_free') ? $request->integer('max_tickets_per_transaction', 1) : 1;
        $validated['umroh_question_enabled'] = $request->boolean('umroh_question_enabled');


        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = 'draft';
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . rand(1000, 9999);
        $validated['meta'] = $this->buildWristbandMeta($request);
        unset(
            $validated['wristband_league_name'],
            $validated['wristband_league_logo'],
            $validated['wristband_home_club_logo'],
            $validated['wristband_away_club_logo'],
            $validated['wristband_sponsor_logos']
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
            'evoucher_info' => 'nullable|string',
            'wristband_league_name' => 'nullable|string|max:255',
            'wristband_league_logo' => 'nullable|image|max:1024',
            'wristband_home_club_logo' => 'nullable|image|max:1024',
            'wristband_away_club_logo' => 'nullable|image|max:1024',
            'wristband_sponsor_logos' => 'nullable|array',
            'wristband_sponsor_logos.*' => 'nullable|image|max:1024',
        ]);

        // DEBUG: Log what terms_conditions was received
        \Log::info('[DEBUG EventController::update] RAW terms_conditions from request', [
            'raw_value' => $request->input('terms_conditions'),
            'raw_length' => strlen($request->input('terms_conditions') ?? ''),
            'validated_value' => $validated['terms_conditions'] ?? 'NOT_IN_VALIDATED',
            'event_id' => $event->id,
        ]);

        $validated['is_free'] = $request->boolean('is_free');
        $validated['max_tickets_per_transaction'] = $request->input('is_free') ? $request->integer('max_tickets_per_transaction', 1) : 1;
        $validated['umroh_question_enabled'] = $request->boolean('umroh_question_enabled');

        $validated['meta'] = $this->buildWristbandMeta($request, $event->meta ?? []);
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

        // DEBUG: Log what will be saved
        \Log::info('[DEBUG EventController::update] About to save', [
            'terms_conditions_to_save' => $validated['terms_conditions'] ?? 'NOT_SET',
            'event_id' => $event->id,
        ]);

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
}
