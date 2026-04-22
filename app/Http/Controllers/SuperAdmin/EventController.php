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
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . rand(1000, 9999);

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
        ]);

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
        $event->forceDelete();
        return redirect()->route('superadmin.events.trash')->with('success', 'Event permanently deleted.');
    }
}
