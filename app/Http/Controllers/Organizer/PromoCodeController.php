<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index()
    {
        $vouchers = PromoCode::where('tenant_id', auth()->user()->tenant_id)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('organizer.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $events = Event::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('organizer.vouchers.create', compact('events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'max_usage' => 'nullable|integer|min:1',
            'start_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:start_at',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        
        PromoCode::create($validated);

        return redirect()->route('organizer.vouchers.index')->with('success', 'Voucher created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
