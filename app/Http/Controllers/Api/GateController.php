<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\GateLog;
use App\Models\Gate;
use App\Models\Event;
use Illuminate\Http\Request;

class GateController extends Controller
{
    /**
     * List Gates for an Event
     */
    public function listGates(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id'
        ]);

        $gates = Gate::where('event_id', $request->event_id)
            ->where('is_active', true)
            ->with(['ticketCategories' => function($q) {
                $q->select('ticket_categories.id', 'name');
            }])
            ->get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => $gates
        ]);
    }

    /**
     * Pemindaian Berkecepatan Tinggi & Anti-Passback
     */
    public function scan(Request $request)
    {
        $request->validate([
            'wristband_qr' => 'required',
            'type' => 'required|in:IN,OUT',
            'gate_id' => 'nullable|exists:gates,id', // Recommended to use gate_id
            'gate_name' => 'nullable', // Fallback for legacy/simple use
            'device_id' => 'nullable'
        ]);

        $scanCode = trim($request->wristband_qr);

        $ticket = Ticket::with(['category', 'transaction'])
            ->where('wristband_qr', $scanCode)
            ->orWhere('ticket_code', $scanCode)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'REJECT',
                'message' => 'Invalid Wristband / Ticket Code',
                'color' => 'pink'
            ], 404);
        }

        // Access Control: Check Gate Mapping
        if ($request->gate_id) {
            $gate = Gate::with('ticketCategories')->find($request->gate_id);
            if ($gate) {
                $allowedCategoryIds = $gate->ticketCategories->pluck('id')->toArray();
                if (!in_array($ticket->ticket_category_id, $allowedCategoryIds)) {
                    return response()->json([
                        'status' => 'REJECT',
                        'message' => 'Wrong Gate! Access Denied for ' . $ticket->category->name,
                        'color' => 'pink',
                        'visitor' => $ticket->transaction->customer_name ?? '-',
                        'category' => $ticket->category->name
                    ], 403);
                }
                // Use gate name from the database if gate_id is provided
                $gateName = $gate->name;
            }
        } else {
            $gateName = $request->gate_name;
        }

        // Anti-Passback Logic
        if ($request->type === 'IN') {
            $lastLog = GateLog::where('ticket_id', $ticket->id)
                ->orderBy('scanned_at', 'desc')
                ->first();

            if ($lastLog && $lastLog->type === 'IN') {
                return response()->json([
                    'status' => 'REJECT',
                    'message' => 'DUPLICATE ENTRY (Anti-Passback)',
                    'color' => 'pink',
                    'visitor' => $ticket->transaction->customer_name ?? '-'
                ], 403);
            }
        }

        // Log the movement
        $log = GateLog::create([
            'tenant_id' => $ticket->tenant_id,
            'event_id' => $ticket->event_id,
            'ticket_id' => $ticket->id,
            'gate_name' => $gateName ?? $request->gate_name,
            'type' => $request->type,
            'scanned_at' => now(),
            'device_id' => $request->device_id,
            'scanned_by' => auth()->id()
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Access Granted: ' . $request->type,
            'visitor' => $ticket->transaction->customer_name ?? '-',
            'category' => $ticket->category->name,
            'color' => 'green'
        ]);
    }

    /**
     * Background Sync (Hibrida Offline-First)
     */
    public function syncLogs(Request $request)
    {
        $logs = $request->input('logs', []); // Array of offline logs
        
        foreach ($logs as $logData) {
            // Check if ticket category is allowed for the gate (for offline logs)
            // Note: This assumes the mobile app already validated it, 
            // but we can re-validate here if needed.
            
            GateLog::updateOrCreate(
                ['meta->offline_id' => $logData['offline_id']],
                array_merge($logData, ['meta' => ['synced_at' => now()]])
            );
        }

        return response()->json(['message' => count($logs) . ' logs synced successfully']);
    }
}
