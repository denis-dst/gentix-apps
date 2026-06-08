<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GateLog;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $baseEventsQuery = Event::query()
            ->where('tenant_id', $tenantId)
            ->when($request->filled('event_id'), fn ($query) => $query->where('id', $request->event_id))
            ->with(['ticketCategories' => fn ($query) => $query->orderBy('sort_order')->orderBy('name')])
            ->orderByDesc('event_start_date');

        $events = (clone $baseEventsQuery)->paginate(8)->withQueryString();
        $summaryRows = (clone $baseEventsQuery)->get()->map(fn ($event) => $this->buildEventReport($event));

        $eventOptions = Event::where('tenant_id', $tenantId)
            ->orderByDesc('event_start_date')
            ->get(['id', 'name']);

        $reportRows = $events->getCollection()
            ->map(fn ($event) => $this->buildEventReport($event));

        $totals = [
            'sold' => $summaryRows->sum('sold_count'),
            'redeemed' => $summaryRows->sum('redeemed_count'),
            'checkin' => $summaryRows->sum('checkin_count'),
            'checkout' => $summaryRows->sum('checkout_count'),
            'inside' => $summaryRows->sum('inside_count'),
            'revenue' => $summaryRows->sum('revenue'),
            'paid_transactions' => $summaryRows->sum('paid_transactions_count'),
        ];

        $transactions = Transaction::where('tenant_id', $tenantId)
            ->when($request->filled('event_id'), fn ($query) => $query->where('event_id', $request->event_id))
            ->with(['event', 'tickets.category'])
            ->orderByDesc('created_at')
            ->get();

        return view('organizer.reports.index', compact('events', 'eventOptions', 'reportRows', 'totals', 'transactions'));
    }

    private function buildEventReport(Event $event): array
    {
        $categoryIds = $event->ticketCategories->pluck('id')->all();

        $ticketStats = Ticket::query()
            ->where('event_id', $event->id)
            ->select('ticket_category_id')
            ->selectRaw("SUM(CASE WHEN status IN ('sold', 'redeemed') THEN 1 ELSE 0 END) as sold_count")
            ->selectRaw("SUM(CASE WHEN status = 'redeemed' THEN 1 ELSE 0 END) as redeemed_count")
            ->groupBy('ticket_category_id')
            ->get()
            ->keyBy('ticket_category_id');

        $gateStats = GateLog::query()
            ->join('tickets', 'tickets.id', '=', 'gate_logs.ticket_id')
            ->where('gate_logs.event_id', $event->id)
            ->select('tickets.ticket_category_id')
            ->selectRaw("SUM(CASE WHEN gate_logs.type = 'IN' THEN 1 ELSE 0 END) as checkin_count")
            ->selectRaw("SUM(CASE WHEN gate_logs.type = 'OUT' THEN 1 ELSE 0 END) as checkout_count")
            ->groupBy('tickets.ticket_category_id')
            ->get()
            ->keyBy('ticket_category_id');

        $transactionStats = Transaction::query()
            ->where('event_id', $event->id)
            ->where('payment_status', 'paid')
            ->select('ticket_category_id')
            ->selectRaw('COUNT(*) as paid_transactions_count')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as revenue')
            ->groupBy('ticket_category_id')
            ->get()
            ->keyBy('ticket_category_id');

        $categories = $event->ticketCategories->map(function ($category) use ($ticketStats, $gateStats, $transactionStats) {
            $ticket = $ticketStats->get($category->id);
            $gate = $gateStats->get($category->id);
            $transaction = $transactionStats->get($category->id);
            $checkin = (int) ($gate->checkin_count ?? 0);
            $checkout = (int) ($gate->checkout_count ?? 0);

            return [
                'id' => $category->id,
                'name' => $category->name,
                'hex_color' => $category->hex_color ?? '#6366F1',
                'sold_count' => (int) ($ticket->sold_count ?? 0),
                'redeemed_count' => (int) ($ticket->redeemed_count ?? 0),
                'checkin_count' => $checkin,
                'checkout_count' => $checkout,
                'inside_count' => max(0, $checkin - $checkout),
                'paid_transactions_count' => (int) ($transaction->paid_transactions_count ?? 0),
                'revenue' => (float) ($transaction->revenue ?? 0),
            ];
        });

        return [
            'event' => $event,
            'categories' => $categories,
            'sold_count' => $categories->sum('sold_count'),
            'redeemed_count' => $categories->sum('redeemed_count'),
            'checkin_count' => $categories->sum('checkin_count'),
            'checkout_count' => $categories->sum('checkout_count'),
            'inside_count' => $categories->sum('inside_count'),
            'paid_transactions_count' => $categories->sum('paid_transactions_count'),
            'revenue' => $categories->sum('revenue'),
        ];
    }
}
