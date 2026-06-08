<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Event;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with('tenant')->paginate(10);
        return view('superadmin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenants = Tenant::where('status', 'active')->get();
        return view('superadmin.events.create', compact('tenants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'gate_open_at' => 'required|date',
            'gate_close_at' => 'required|date|after:gate_open_at',
            'status' => 'required|in:draft,published,finished,cancelled',
            'banner_image' => 'nullable|image|max:2048',
            'evoucher_info' => 'nullable|string',
            'wristband_league_name' => 'nullable|string|max:255',
            'wristband_league_logo' => 'nullable|image|max:1024',
            'wristband_home_club_logo' => 'nullable|image|max:1024',
            'wristband_away_club_logo' => 'nullable|image|max:1024',
            'wristband_sponsor_logos' => 'nullable|array',
            'wristband_sponsor_logos.*' => 'nullable|image|max:1024',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . rand(1000, 9999);
        $validated['meta'] = $this->buildWristbandMeta($request);
        unset(
            $validated['wristband_league_name'],
            $validated['wristband_league_logo'],
            $validated['wristband_home_club_logo'],
            $validated['wristband_away_club_logo'],
            $validated['wristband_sponsor_logos']
        );

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }

        Event::create($validated);

        return redirect()->route('superadmin.events.index')->with('success', 'Event created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $tenants = Tenant::where('status', 'active')->get();
        return view('superadmin.events.edit', compact('event', 'tenants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'gate_open_at' => 'required|date',
            'gate_close_at' => 'required|date|after:gate_open_at',
            'status' => 'required|in:draft,published,finished,cancelled',
            'banner_image' => 'nullable|image|max:2048',
            'evoucher_info' => 'nullable|string',
            'wristband_league_name' => 'nullable|string|max:255',
            'wristband_league_logo' => 'nullable|image|max:1024',
            'wristband_home_club_logo' => 'nullable|image|max:1024',
            'wristband_away_club_logo' => 'nullable|image|max:1024',
            'wristband_sponsor_logos' => 'nullable|array',
            'wristband_sponsor_logos.*' => 'nullable|image|max:1024',
        ]);
        $validated['meta'] = $this->buildWristbandMeta($request, $event->meta ?? []);
        unset(
            $validated['wristband_league_name'],
            $validated['wristband_league_logo'],
            $validated['wristband_home_club_logo'],
            $validated['wristband_away_club_logo'],
            $validated['wristband_sponsor_logos']
        );

        if ($request->hasFile('banner_image')) {
            if ($event->banner_image) {
                Storage::disk('public')->delete($event->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('events/banners', 'public');
        }

        $event->update($validated);

        return redirect()->route('superadmin.events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('superadmin.events.index')->with('success', 'Event moved to trash.');
    }

    /**
     * Display a listing of trashed resources.
     */
    public function trash()
    {
        $events = Event::onlyTrashed()->with('tenant')->paginate(10);
        return view('superadmin.events.trash', compact('events'));
    }

    /**
     * Restore the specified resource.
     */
    public function restore($id)
    {
        $event = Event::withTrashed()->findOrFail($id);
        $event->restore();
        return redirect()->route('superadmin.events.trash')->with('success', 'Event restored successfully.');
    }

    /**
     * Permanently delete.
     */
    public function forceDelete($id)
    {
        $event = Event::withTrashed()->findOrFail($id);
        if ($event->banner_image) {
            Storage::disk('public')->delete($event->banner_image);
        }
        foreach (['wristband_league_logo', 'wristband_home_club_logo', 'wristband_away_club_logo'] as $key) {
            if (!empty($event->meta[$key])) {
                Storage::disk('public')->delete($event->meta[$key]);
            }
        }
        foreach (($event->meta['wristband_sponsor_logos'] ?? []) as $logo) {
            Storage::disk('public')->delete($logo);
        }
        $event->forceDelete();
        return redirect()->route('superadmin.events.trash')->with('success', 'Event permanently deleted.');
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
