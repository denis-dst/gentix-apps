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

        $categories = $event->ticketCategories;
        return view('organizer.gate.setup', compact('event', 'categories'));
    }

    public function setup(Request $request, Event $event)
    {
        $request->validate([
            'gate_category_id' => 'required|exists:ticket_categories,id',
            'gate_mode' => 'required|in:IN,OUT',
        ]);

        $category = \App\Models\TicketCategory::findOrFail($request->gate_category_id);

        session(['gate_category_id' => $category->id]);
        session(['gate_name' => $category->name]);
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

        // Access Control: Check if ticket category matches the gate
        $gateCategoryId = session('gate_category_id');
        if ($gateCategoryId && $ticket->ticket_category_id != $gateCategoryId) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Tiket Salah! (Harus ' . session('gate_name') . ')',
                'customer' => $ticket->transaction->customer_name,
                'category' => $ticket->category->name
            ], 403);
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
                'customer' => $ticket->transaction->customer_name,
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
}
