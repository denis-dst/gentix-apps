<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketCategoryController extends Controller
{
    public function create(Event $event)
    {
        $this->authorizeTenant($event);
        return view('organizer.tickets.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorizeTenant($event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'nik_restriction' => 'nullable|string|max:255',
            'nik_restriction_message' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:255',
            'quota' => 'required|integer|min:1',
            'sale_start_at' => 'nullable|date',
            'sale_end_at' => 'nullable|date|after:sale_start_at',
            'hex_color' => 'nullable|string|size:7',
            'category_image' => 'nullable|image|max:1024',
            'background_image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        $data['event_id'] = $event->id;
        $data['tenant_id'] = $event->tenant_id;

        if ($request->hasFile('category_image')) {
            $data['category_image'] = $request->file('category_image')->store('tickets/categories', 'public');
        }

        if ($request->hasFile('background_image')) {
            $data['background_image'] = $request->file('background_image')->store('tickets/backgrounds', 'public');
        }

        TicketCategory::create($data);

        return redirect()->route('organizer.events.edit', $event)->with('success', 'Ticket category created.');
    }

    public function edit(Event $event, TicketCategory $category)
    {
        $this->authorizeTenant($event);
        if ($category->event_id !== $event->id) abort(403);

        return view('organizer.tickets.edit', compact('event', 'category'));
    }

    public function update(Request $request, Event $event, TicketCategory $category)
    {
        $this->authorizeTenant($event);
        if ($category->event_id !== $event->id) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'nik_restriction' => 'nullable|string|max:255',
            'nik_restriction_message' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:255',
            'quota' => 'required|integer|min:1',
            'sale_start_at' => 'nullable|date',
            'sale_end_at' => 'nullable|date|after:sale_start_at',
            'hex_color' => 'nullable|string|size:7',
            'category_image' => 'nullable|image|max:1024',
            'background_image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;

        if ($request->hasFile('category_image')) {
            if ($category->category_image) Storage::disk('public')->delete($category->category_image);
            $data['category_image'] = $request->file('category_image')->store('tickets/categories', 'public');
        }

        if ($request->hasFile('background_image')) {
            if ($category->background_image) Storage::disk('public')->delete($category->background_image);
            $data['background_image'] = $request->file('background_image')->store('tickets/backgrounds', 'public');
        }

        $category->update($data);

        return redirect()->route('organizer.events.edit', $event)->with('success', 'Ticket category updated.');
    }

    public function destroy(Event $event, TicketCategory $category)
    {
        $this->authorizeTenant($event);
        if ($category->event_id !== $event->id) abort(403);

        $category->delete();
        return redirect()->route('organizer.events.edit', $event)->with('success', 'Ticket category deleted.');
    }

    private function authorizeTenant(Event $event)
    {
        if ($event->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized access to this event');
        }
    }
}
