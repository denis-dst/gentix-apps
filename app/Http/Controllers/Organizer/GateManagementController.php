<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Gate;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

class GateManagementController extends Controller
{
    public function index(Event $event)
    {
        $this->authorizeTenant($event);
        $gates = $event->gates()->with('ticketCategories')->get();
        return view('organizer.gates.index', compact('event', 'gates'));
    }

    public function create(Event $event)
    {
        $this->authorizeTenant($event);
        $categories = $event->ticketCategories;
        return view('organizer.gates.create', compact('event', 'categories'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorizeTenant($event);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:ticket_categories,id',
        ]);

        $gate = $event->gates()->create([
            'tenant_id' => $event->tenant_id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        $gate->ticketCategories()->sync($validated['category_ids']);

        return redirect()->route('organizer.events.gates.index', $event)->with('success', 'Gate created successfully.');
    }

    public function edit(Event $event, Gate $gate)
    {
        $this->authorizeTenant($event);
        $categories = $event->ticketCategories;
        $gate->load('ticketCategories');
        return view('organizer.gates.edit', compact('event', 'gate', 'categories'));
    }

    public function update(Request $request, Event $event, Gate $gate)
    {
        $this->authorizeTenant($event);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:ticket_categories,id',
        ]);

        $gate->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active'),
        ]);

        $gate->ticketCategories()->sync($validated['category_ids']);

        return redirect()->route('organizer.events.gates.index', $event)->with('success', 'Gate updated successfully.');
    }

    public function destroy(Event $event, Gate $gate)
    {
        $this->authorizeTenant($event);
        $gate->delete();
        return redirect()->route('organizer.events.gates.index', $event)->with('success', 'Gate deleted successfully.');
    }

    private function authorizeTenant(Event $event)
    {
        if ($event->tenant_id !== auth()->user()->tenant_id && !auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Unauthorized access to this event');
        }
    }
}
