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
            'google_maps_url' => 'nullable|url|max:2048',
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
            'terms_conditions' => 'nullable|string',
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
            $validated['banner_image'] = \App\Services\ImageOptimizerService::uploadAndOptimize($request->file('banner_image'), 'events/banners', 1200, 80);
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
            'google_maps_url' => 'nullable|url|max:2048',
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
            'terms_conditions' => 'nullable|string',
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
            $validated['banner_image'] = \App\Services\ImageOptimizerService::uploadAndOptimize($request->file('banner_image'), 'events/banners', 1200, 80);
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
     * Duplikasi / Copy Event beserta seluruh kategori tiket dan gate
     * Kecuali kode verifikasi event (dibuatkan baru secara acak)
     */
    public function duplicate(Event $event)
    {
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
            $originalCategories = \App\Models\TicketCategory::where('event_id', $event->id)->get();
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

            return redirect()->route('superadmin.events.edit', $newEvent)
                ->with('success', 'Event berhasil diduplikasi beserta seluruh kategori tiket dan gerbang gate! Kode verifikasi baru telah di-generate.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menduplikasi event: ' . $e->getMessage());
        }
    }
}
