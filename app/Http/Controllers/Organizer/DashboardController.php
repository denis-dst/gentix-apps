<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Gate;
use App\Models\GateLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $events = Event::withCount(['tickets', 'ticketCategories'])
            ->where('tenant_id', $tenantId)
            ->orderBy('event_start_date', 'desc')
            ->get();

        $eventOptions = Event::where('tenant_id', $tenantId)
            ->orderBy('event_start_date', 'desc')
            ->get(['id', 'name']);

        $selectedEventId = $request->get('event_id');
        $selectedGate = $request->get('gate_name');
        $selectedType = $request->get('scan_type');
        $sort = $request->get('sort', 'earliest');
        $search = $request->get('search');

        $gateOptions = GateLog::where('tenant_id', $tenantId)
            ->when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
            ->whereNotNull('gate_name')
            ->where('gate_name', '!=', '')
            ->distinct()
            ->pluck('gate_name')
            ->merge(
                Gate::where('tenant_id', $tenantId)
                    ->when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
                    ->pluck('name')
            )
            ->unique()
            ->filter()
            ->values();

        $scanQuery = GateLog::query()
            ->where('tenant_id', $tenantId)
            ->when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
            ->when($selectedGate, fn ($q) => $q->where('gate_name', $selectedGate))
            ->when($selectedType, fn ($q) => $q->where('type', $selectedType))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('ticket', function ($tQ) use ($search) {
                        $tQ->where('ticket_code', 'like', "%{$search}%")
                           ->orWhere('wristband_qr', 'like', "%{$search}%")
                           ->orWhere('visitor_data', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ticket.transaction', function ($trQ) use ($search) {
                        $trQ->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('customer_email', 'like', "%{$search}%")
                            ->orWhere('customer_phone', 'like', "%{$search}%");
                    })
                    ->orWhere('gate_name', 'like', "%{$search}%");
                });
            })
            ->with(['ticket.category', 'ticket.transaction', 'event', 'scanner']);

        $totalScans = (clone $scanQuery)->count();
        $totalCheckIn = (clone $scanQuery)->where('type', 'IN')->count();
        $totalCheckOut = (clone $scanQuery)->where('type', 'OUT')->count();

        $earliestScan = GateLog::where('tenant_id', $tenantId)
            ->when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
            ->when($selectedGate, fn ($q) => $q->where('gate_name', $selectedGate))
            ->where('type', 'IN')
            ->with(['ticket.category', 'ticket.transaction', 'event', 'scanner'])
            ->orderBy('scanned_at', 'asc')
            ->first();

        if ($sort === 'latest') {
            $scanQuery->orderBy('scanned_at', 'desc');
        } else {
            $scanQuery->orderBy('scanned_at', 'asc');
        }

        $scanLogs = $scanQuery->paginate(10)->withQueryString();

        return view('organizer.dashboard', compact(
            'events',
            'eventOptions',
            'gateOptions',
            'scanLogs',
            'totalScans',
            'totalCheckIn',
            'totalCheckOut',
            'earliestScan',
            'selectedEventId',
            'selectedGate',
            'selectedType',
            'sort',
            'search'
        ));
    }
}
