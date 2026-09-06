<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Gate;
use App\Models\GateLog;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'total_events' => Event::count(),
            'total_transactions' => Transaction::where('payment_status', 'paid')->count(),
            'total_revenue' => Transaction::where('payment_status', 'paid')->sum('total_amount'),
            'total_tickets' => Ticket::count(),
            'total_users' => User::count(),
        ];

        $recent_transactions = Transaction::with(['event', 'buyer'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $active_events = Event::where('status', 'published')
            ->with('tenant')
            ->withCount('tickets')
            ->orderBy('event_start_date', 'asc')
            ->take(5)
            ->get();

        // Scan history and early arrival detection
        $tenantOptions = Tenant::orderBy('name')->get(['id', 'name']);
        $eventOptions = Event::orderBy('event_start_date', 'desc')->get(['id', 'name', 'tenant_id']);

        $selectedTenantId = $request->get('tenant_id');
        $selectedEventId = $request->get('event_id');
        $selectedGate = $request->get('gate_name');
        $selectedType = $request->get('scan_type');
        $sort = $request->get('sort', 'earliest');
        $search = $request->get('search');

        $gateOptions = GateLog::query()
            ->when($selectedTenantId, fn ($q) => $q->where('tenant_id', $selectedTenantId))
            ->when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
            ->whereNotNull('gate_name')
            ->where('gate_name', '!=', '')
            ->distinct()
            ->pluck('gate_name')
            ->merge(
                Gate::query()
                    ->when($selectedTenantId, fn ($q) => $q->where('tenant_id', $selectedTenantId))
                    ->when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
                    ->pluck('name')
            )
            ->unique()
            ->filter()
            ->values();

        $scanQuery = GateLog::query()
            ->when($selectedTenantId, fn ($q) => $q->where('tenant_id', $selectedTenantId))
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
            ->with(['ticket.category', 'ticket.transaction', 'event.tenant', 'scanner']);

        $totalScans = (clone $scanQuery)->count();
        $totalCheckIn = (clone $scanQuery)->where('type', 'IN')->count();
        $totalCheckOut = (clone $scanQuery)->where('type', 'OUT')->count();

        $earliestScan = GateLog::query()
            ->when($selectedTenantId, fn ($q) => $q->where('tenant_id', $selectedTenantId))
            ->when($selectedEventId, fn ($q) => $q->where('event_id', $selectedEventId))
            ->when($selectedGate, fn ($q) => $q->where('gate_name', $selectedGate))
            ->where('type', 'IN')
            ->with(['ticket.category', 'ticket.transaction', 'event.tenant', 'scanner'])
            ->orderBy('scanned_at', 'asc')
            ->first();

        if ($sort === 'latest') {
            $scanQuery->orderBy('scanned_at', 'desc');
        } else {
            $scanQuery->orderBy('scanned_at', 'asc');
        }

        $scanLogs = $scanQuery->paginate(10)->withQueryString();

        return view('superadmin.dashboard', compact(
            'stats',
            'recent_transactions',
            'active_events',
            'tenantOptions',
            'eventOptions',
            'gateOptions',
            'scanLogs',
            'totalScans',
            'totalCheckIn',
            'totalCheckOut',
            'earliestScan',
            'selectedTenantId',
            'selectedEventId',
            'selectedGate',
            'selectedType',
            'sort',
            'search'
        ));
    }
}
