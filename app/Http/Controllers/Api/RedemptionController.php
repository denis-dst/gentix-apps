<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedemptionController extends Controller
{
    /**
     * Get ticket details by code for scanning.
     */
    public function show($code)
    {
        $ticket = Ticket::with(['event', 'category', 'transaction'])
            ->where('ticket_code', $code)
            ->first();

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        return response()->json([
            'ticket' => $ticket,
            'can_redeem' => $ticket->status === 'sold'
        ]);
    }

    /**
     * Redeem ticket and link to wristband.
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string|exists:tickets,ticket_code',
            'wristband_qr' => 'nullable|string|unique:tickets,wristband_qr'
        ]);

        $ticket = Ticket::where('ticket_code', $request->ticket_code)->firstOrFail();

        if ($ticket->status === 'redeemed') {
            return response()->json([
                'message' => 'Ticket has already been redeemed at ' . $ticket->redeemed_at->format('d M Y H:i'),
                'redeemed_at' => $ticket->redeemed_at
            ], 422);
        }

        if ($ticket->status !== 'sold') {
            return response()->json(['message' => 'Ticket is not in a redeemable status: ' . $ticket->status], 422);
        }

        try {
            DB::beginTransaction();

            $ticket->update([
                'status' => 'redeemed',
                'wristband_qr' => $request->wristband_qr,
                'redeemed_at' => now(),
                'redeemed_by' => auth()->id() // Assumes scanner is logged in
            ]);

            // Log the redemption in GateLog if needed
            $ticket->gateLogs()->create([
                'tenant_id' => $ticket->tenant_id,
                'event_id' => $ticket->event_id,
                'gate_id' => $request->gate_id ?? null,
                'log_type' => 'redemption',
                'scan_status' => 'success',
                'raw_payload' => json_encode($request->all())
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Ticket successfully redeemed!',
                'ticket' => $ticket->fresh(['category', 'event'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to redeem ticket: ' . $e->getMessage()], 500);
        }
    }
}
