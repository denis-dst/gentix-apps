<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventProviderController extends Controller
{
    /**
     * Manajemen Acara (Event CRUD)
     */
    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'venue' => 'required',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date',
            'gate_open_at' => 'nullable|date',
            'gate_close_at' => 'nullable|date',
            'background_image' => 'nullable|image'
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['slug'] = str($request->name)->slug() . '-' . rand(100, 999);

        if ($request->hasFile('background_image')) {
            $validated['background_image'] = $request->file('background_image')->store('events/bg', 'public');
        }

        $event = Event::create($validated);
        return response()->json($event, 201);
    }

    /**
     * Kustomisasi Visual Kategori Tiket
     */
    public function storeTicketCategory(Request $request, Event $event)
    {
        $this->authorizeTenant($event);

        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'quota' => 'required|integer',
            'hex_color' => 'required|string|size:7', // e.g. #FF5733
            'category_image' => 'nullable|image',
            'background_image' => 'nullable|image',
            'layout_config' => 'nullable|array'
        ]);

        $validated['event_id'] = $event->id;
        $validated['tenant_id'] = $event->tenant_id;

        if ($request->hasFile('category_image')) {
            $validated['category_image'] = $request->file('category_image')->store('tickets/categories', 'public');
        }

        if ($request->hasFile('background_image')) {
            $validated['background_image'] = $request->file('background_image')->store('tickets/backgrounds', 'public');
        }

        $category = TicketCategory::create($validated);
        return response()->json($category, 201);
    }

    /**
     * Update Desain Kategori Tiket
     */
    public function updateTicketDesign(Request $request, TicketCategory $category)
    {
        $this->authorizeTenant($category->event);

        $validated = $request->validate([
            'hex_color' => 'nullable|string|size:7',
            'category_image' => 'nullable|image',
            'background_image' => 'nullable|image',
            'layout_config' => 'nullable|array'
        ]);

        if ($request->hasFile('category_image')) {
            if ($category->category_image) Storage::disk('public')->delete($category->category_image);
            $validated['category_image'] = $request->file('category_image')->store('tickets/categories', 'public');
        }

        if ($request->hasFile('background_image')) {
            if ($category->background_image) Storage::disk('public')->delete($category->background_image);
            $validated['background_image'] = $request->file('background_image')->store('tickets/backgrounds', 'public');
        }

        $category->update($validated);
        return response()->json($category);
    }

    /**
     * Dasbor Analitik Real-Time
     */
    public function getAnalytics(Event $event)
    {
        $this->authorizeTenant($event);

        $occupancy = $event->current_occupancy;
        
        $tickets_stats = $event->ticketCategories()
            ->select('name', 'quota', 'sold_count')
            ->get();

        $revenue = $event->transactions()->where('payment_status', 'paid')->sum('total_amount');

        return response()->json([
            'event_name' => $event->name,
            'current_occupancy' => $occupancy,
            'tickets_stats' => $tickets_stats,
            'total_revenue' => $revenue,
            'redeemed_count' => $event->tickets()->where('status', 'redeemed')->count()
        ]);
    }

    private function authorizeTenant(Event $event)
    {
        if ($event->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized access to this event');
        }
    }
}
