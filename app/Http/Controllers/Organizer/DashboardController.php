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
        
        // Single query for tenant events
        $events = Event::withCount(['tickets', 'ticketCategories'])
            ->where('tenant_id', $tenantId)
            ->orderBy('event_start_date', 'desc')
            ->get();

        // Reuse in-memory events collection for dropdown options to eliminate duplicate query
        $eventOptions = $events->map(fn ($e) => (object) ['id' => $e->id, 'name' => $e->name]);

        $selectedEventId = $request->get('event_id');
        $selectedGate = $request->get('gate_name');
        $selectedType = $request->get('scan_type');
        $sort = $request->get('sort', 'earliest');
        $search = $request->get('search');

        // Distinct gates query
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
            });

        // Single aggregate query for total scans, check-ins, and check-outs
        $statsAggregate = (clone $scanQuery)
            ->selectRaw("
                COUNT(*) as total_scans,
                SUM(CASE WHEN type = 'IN' THEN 1 ELSE 0 END) as total_checkin,
                SUM(CASE WHEN type = 'OUT' THEN 1 ELSE 0 END) as total_checkout
            ")
            ->first();

        $totalScans = (int) ($statsAggregate->total_scans ?? 0);
        $totalCheckIn = (int) ($statsAggregate->total_checkin ?? 0);
        $totalCheckOut = (int) ($statsAggregate->total_checkout ?? 0);

        // Earliest attendee with specific eager columns
        $earliestScan = GateLog::where('tenant_id', $tenantId)
            ->when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
            ->when($selectedGate, fn ($q) => $q->where('gate_name', $selectedGate))
            ->where('type', 'IN')
            ->with([
                'ticket:id,ticket_code,wristband_qr,visitor_data,transaction_id,ticket_category_id',
                'ticket.category:id,name,hex_color',
                'ticket.transaction:id,customer_name',
                'event:id,name',
                'scanner:id,name'
            ])
            ->orderBy('scanned_at', 'asc')
            ->first();

        if ($sort === 'latest') {
            $scanQuery->orderBy('scanned_at', 'desc');
        } else {
            $scanQuery->orderBy('scanned_at', 'asc');
        }

        $scanLogs = $scanQuery
            ->with([
                'ticket:id,ticket_code,wristband_qr,visitor_data,transaction_id,ticket_category_id',
                'ticket.category:id,name,hex_color',
                'ticket.transaction:id,customer_name,customer_email,customer_phone',
                'event:id,name',
                'scanner:id,name'
            ])
            ->paginate(10)
            ->withQueryString();

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
