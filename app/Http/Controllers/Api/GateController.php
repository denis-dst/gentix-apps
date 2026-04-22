<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\GateLog;
use Illuminate\Http\Request;

class GateController extends Controller
{
    /**
     * Pemindaian Berkecepatan Tinggi & Anti-Passback
     */
    public function scan(Request $request)
    {
        $request->validate([
            'wristband_qr' => 'required',
            'type' => 'required|in:IN,OUT',
            'gate_name' => 'nullable',
            'device_id' => 'nullable'
        ]);

        $ticket = Ticket::where('wristband_qr', $request->wristband_qr)->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'REJECT',
                'message' => 'Invalid Wristband',
                'color' => 'pink'
            ], 404);
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
                    'visitor' => $ticket->transaction->customer_name
                ], 403);
            }
        }

        // Log the movement
        $log = GateLog::create([
            'tenant_id' => $ticket->tenant_id,
            'event_id' => $ticket->event_id,
            'ticket_id' => $ticket->id,
            'gate_name' => $request->gate_name,
            'type' => $request->type,
            'scanned_at' => now(),
            'device_id' => $request->device_id,
            'scanned_by' => auth()->id()
        ]);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Access Granted: ' . $request->type,
            'visitor' => $ticket->transaction->customer_name,
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
            // Processing offline logs and handling potential conflicts
            // Usually involves checking timestamps and avoiding duplicates
            GateLog::updateOrCreate(
                ['meta->offline_id' => $logData['offline_id']],
                array_merge($logData, ['meta' => ['synced_at' => now()]])
            );
        }

        return response()->json(['message' => count($logs) . ' logs synced successfully']);
    }
}
