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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'security_code' => 'nullable|string|size:6',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = 'draft';
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']) . '-' . rand(1000, 9999);
        
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'status' => 'required|in:draft,published,cancelled',
            'background_image' => 'nullable|image|max:2048',
            'security_code' => 'required|string|size:6',
        ]);

        if ($request->hasFile('background_image')) {
            if ($event->background_image) Storage::disk('public')->delete($event->background_image);
            $validated['background_image'] = $request->file('background_image')->store('events/backgrounds', 'public');
        }

        $event->update($validated);

        return redirect()->route('organizer.events.edit', $event)->with('success', 'Event updated successfully.');
    }

    private function authorizeTenant(Event $event)
    {
        if ($event->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized access to this event');
        }
    }
}
