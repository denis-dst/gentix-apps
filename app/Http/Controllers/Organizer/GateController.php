<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\GateLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GateController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $events = Event::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->orderBy('event_start_date', 'desc')
            ->get();

        return view('organizer.gate.index', compact('events'));
    }

    public function verifyForm(Event $event)
    {
        return view('organizer.gate.verify', compact('event'));
    }

    public function verify(Request $request, Event $event)
    {
        $request->validate([
            'security_code' => 'required',
        ]);

        if ($event->security_code && $request->security_code !== $event->security_code) {
            return back()->with('error', 'Kode Keamanan Salah!');
        }

        session(['gate_event_id' => $event->id]);
        session()->forget(['gate_name', 'gate_mode']); // Reset setup

        return redirect()->route('organizer.gate.setup', $event);
    }

    public function setupForm(Event $event)
    {
        if (session('gate_event_id') != $event->id) {
            return redirect()->route('organizer.gate.verify', $event);
        }

        $gates = $event->gates()->where('is_active', true)->get();
        $categories = $event->ticketCategories; // Keep for legacy fallback if no gates defined
        return view('organizer.gate.setup', compact('event', 'gates', 'categories'));
    }

    public function setup(Request $request, Event $event)
    {
        $request->validate([
            'gate_id' => 'nullable|exists:gates,id',
            'gate_category_id' => 'nullable|exists:ticket_categories,id',
            'gate_mode' => 'required|in:IN,OUT',
        ]);

        if ($request->gate_id) {
            $gate = \App\Models\Gate::with('ticketCategories')->findOrFail($request->gate_id);
            session(['gate_id' => $gate->id]);
            session(['gate_name' => $gate->name]);
            session(['gate_allowed_categories' => $gate->ticketCategories->pluck('id')->toArray()]);
            session()->forget('gate_category_id');
        } else {
            // Legacy fallback
            $category = \App\Models\TicketCategory::findOrFail($request->gate_category_id);
            session(['gate_category_id' => $category->id]);
            session(['gate_name' => $category->name]);
            session(['gate_allowed_categories' => [$category->id]]);
            session()->forget('gate_id');
        }

        session(['gate_mode' => $request->gate_mode]);

        return redirect()->route('organizer.gate.scan', $event);
    }

    public function scan(Event $event)
    {
        if (session('gate_event_id') != $event->id || !session('gate_name')) {
            return redirect()->route('organizer.gate.setup', $event);
        }

        // Get initial counts
        $inCount = GateLog::where('event_id', $event->id)->where('type', 'IN')->count();
        $outCount = GateLog::where('event_id', $event->id)->where('type', 'OUT')->count();

        return view('organizer.gate.scan', compact('event', 'inCount', 'outCount'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
            'event_id' => 'required|exists:events,id',
            'mode' => 'required|in:IN,OUT',
            'gate_name' => 'required|string'
        ]);

        $event = Event::findOrFail($request->event_id);
        $ticket = Ticket::where('event_id', $event->id)
            ->where('ticket_code', $request->ticket_code)
            ->with(['transaction', 'category'])
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket Tidak Valid!'
            ], 404);
        }

        // Access Control: Check if ticket category is allowed for the gate
        $allowedCategories = session('gate_allowed_categories', []);
        if (!empty($allowedCategories) && !in_array($ticket->ticket_category_id, $allowedCategories)) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Tiket Salah! (Tidak diizinkan di Gate ' . session('gate_name') . ')',
                'customer' => $ticket->transaction->customer_name,
                'category' => $ticket->category->name
            ], 403);
        }

        // For group checking (transactions with quantity > 1), we return the group details
        $transaction = $ticket->transaction;
        if ($transaction && $transaction->tickets()->count() > 1) {
            $ticketsInGroup = $transaction->tickets()->with(['category', 'gateLogs' => function($q) {
                $q->orderBy('scanned_at', 'desc');
            }])->get();

            $attendeesList = $ticketsInGroup->map(function($t) {
                $lastLog = $t->gateLogs->first();
                $isCheckedIn = $lastLog && $lastLog->type === 'IN';
                return [
                    'ticket_id' => $t->id,
                    'ticket_code' => $t->ticket_code,
                    'name' => $t->visitor_data['name'] ?? $t->transaction->customer_name,
                    'gender' => $t->visitor_data['gender'] ?? null,
                    'is_checked_in' => $isCheckedIn,
                    'category' => $t->category->name
                ];
            });

            return response()->json([
                'success' => true,
                'is_group' => true,
                'message' => 'Detail grup ditemukan',
                'customer' => $transaction->customer_name,
                'category' => $ticket->category->name,
                'attendees' => $attendeesList,
                'scanned_ticket_id' => $ticket->id
            ]);
        }

        // Logic check for IN/OUT
        if ($request->mode === 'IN') {
            // Check if already IN and not yet OUT
            $lastLog = GateLog::where('ticket_id', $ticket->id)
                ->orderBy('scanned_at', 'desc')
                ->first();

            if ($lastLog && $lastLog->type === 'IN') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket sudah berada di dalam area!',
                    'customer' => $ticket->transaction->customer_name,
                    'category' => $ticket->category->name
                ], 400);
            }
        } else {
            // Mode OUT: Check if ever IN
            $hasIn = GateLog::where('ticket_id', $ticket->id)
                ->where('type', 'IN')
                ->exists();

            if (!$hasIn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket belum pernah Check-in!',
                    'customer' => $ticket->transaction->customer_name,
                    'category' => $ticket->category->name
                ], 400);
            }

            $lastLog = GateLog::where('ticket_id', $ticket->id)
                ->orderBy('scanned_at', 'desc')
                ->first();

            if ($lastLog && $lastLog->type === 'OUT') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket sudah berada di luar area!',
                    'customer' => $ticket->transaction->customer_name,
                    'category' => $ticket->category->name
                ], 400);
            }
        }

        try {
            DB::transaction(function() use ($request, $ticket, $event) {
                GateLog::create([
                    'tenant_id' => $event->tenant_id,
                    'event_id' => $event->id,
                    'ticket_id' => $ticket->id,
                    'gate_name' => $request->gate_name,
                    'type' => $request->mode,
                    'scanned_at' => now(),
                    'scanned_by' => auth()->id()
                ]);
            });

            $inCount = GateLog::where('event_id', $event->id)->where('type', 'IN')->count();
            $outCount = GateLog::where('event_id', $event->id)->where('type', 'OUT')->count();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil ' . ($request->mode === 'IN' ? 'Check-in' : 'Check-out'),
                'customer' => $ticket->visitor_data['name'] ?? $ticket->transaction->customer_name,
                'category' => $ticket->category->name,
                'in_count' => $inCount,
                'out_count' => $outCount,
                'occupancy' => $inCount - $outCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkCheckin(Request $request, Event $event)
    {
        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id',
            'mode' => 'required|in:IN,OUT',
            'gate_name' => 'required|string'
        ]);

        try {
            DB::transaction(function() use ($request, $event) {
                foreach ($request->ticket_ids as $ticketId) {
                    // Check logic status first to prevent duplicate active state
                    $lastLog = GateLog::where('ticket_id', $ticketId)
                        ->orderBy('scanned_at', 'desc')
                        ->first();

                    $alreadyInState = $lastLog && $lastLog->type === $request->mode;

                    // If checking in, make sure it's not already in. If checking out, make sure it's checked in first
                    if ($request->mode === 'IN') {
                        if ($alreadyInState) continue;
                    } else {
                        $hasIn = GateLog::where('ticket_id', $ticketId)->where('type', 'IN')->exists();
                        if (!$hasIn || $alreadyInState) continue;
                    }

                    GateLog::create([
                        'tenant_id' => $event->tenant_id,
                        'event_id' => $event->id,
                        'ticket_id' => $ticketId,
                        'gate_name' => $request->gate_name,
                        'type' => $request->mode,
                        'scanned_at' => now(),
                        'scanned_by' => auth()->id()
                    ]);
                }
            });

            $inCount = GateLog::where('event_id', $event->id)->where('type', 'IN')->count();
            $outCount = GateLog::where('event_id', $event->id)->where('type', 'OUT')->count();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil memproses check-in masal',
                'in_count' => $inCount,
                'out_count' => $outCount,
                'occupancy' => $inCount - $outCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
