<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Exports\OperationalReportExport;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GateLog;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $baseEventsQuery = Event::query()
            ->when($request->filled('tenant_id'), fn ($query) => $query->where('tenant_id', $request->tenant_id))
            ->when($request->filled('event_id'), fn ($query) => $query->where('id', $request->event_id))
            ->with([
                'tenant',
                'ticketCategories' => fn ($query) => $query->orderBy('sort_order')->orderBy('name'),
            ])
            ->orderByDesc('event_start_date');

        $events = (clone $baseEventsQuery)->paginate(8)->withQueryString();
        $summaryRows = (clone $baseEventsQuery)->get()->map(fn ($event) => $this->buildEventReport($event));

        $tenantOptions = Tenant::orderBy('name')->get(['id', 'name']);
        $eventOptions = Event::query()
            ->when($request->filled('tenant_id'), fn ($query) => $query->where('tenant_id', $request->tenant_id))
            ->orderByDesc('event_start_date')
            ->get(['id', 'name', 'tenant_id']);

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

        $transactions = Transaction::query()
            ->when($request->filled('tenant_id'), fn ($query) => $query->where('tenant_id', $request->tenant_id))
            ->when($request->filled('event_id'), fn ($query) => $query->where('event_id', $request->event_id))
            ->with(['event', 'tenant', 'category', 'tickets.category', 'tickets.gateLogs'])
            ->orderByDesc('created_at')
            ->get();

        $transactionReportRows = $this->buildTransactionReportRows($transactions);
        $ticketReportRows = $this->buildTicketReportRows($transactions);

        return view('superadmin.reports.index', compact('events', 'tenantOptions', 'eventOptions', 'reportRows', 'totals', 'transactions', 'transactionReportRows', 'ticketReportRows'));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $transactions = Transaction::query()
            ->when($request->filled('tenant_id'), fn ($query) => $query->where('tenant_id', $request->tenant_id))
            ->when($request->filled('event_id'), fn ($query) => $query->where('event_id', $request->event_id))
            ->with(['event', 'tenant', 'category', 'tickets.category', 'tickets.gateLogs'])
            ->orderByDesc('created_at')
            ->get();

        $rows = $this->buildTicketReportRows($transactions)
            ->filter(fn (array $row) => ($row['status'] ?? '') !== 'void');

        $filename = 'Laporan_Pendaftar_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new OperationalReportExport($rows), $filename);
    }

    private function buildTransactionReportRows($transactions)
    {
        return $transactions
            ->map(function ($transaction) {
                $proofTicket = $transaction->tickets->first(function ($ticket) {
                    $visitorData = is_array($ticket->visitor_data) ? $ticket->visitor_data : [];

                    return !empty($visitorData['proof_ig']) || !empty($visitorData['proof_review']) || !empty($visitorData['proofs']);
                });

                $proofData = $proofTicket && is_array($proofTicket->visitor_data) ? $proofTicket->visitor_data : [];

                // Partial scan detection: use gate_logs (type=IN), NOT ticket.status
                $nonVoidTickets   = $transaction->tickets->filter(fn ($t) => $t->status !== 'void');
                $totalTickets     = $nonVoidTickets->count();
                $checkedInCount   = $nonVoidTickets->filter(fn ($t) => $t->gateLogs->where('type', 'IN')->isNotEmpty())->count();
                $isPartialScan    = $totalTickets > 1 && $checkedInCount > 0 && $checkedInCount < $totalTickets;

                return [
                    'reference_no' => $transaction->reference_no,
                    'created_at' => $transaction->created_at,
                    'customer_name' => $transaction->customer_name,
                    'customer_nik' => $transaction->customer_nik,
                    'customer_email' => $transaction->customer_email,
                    'customer_phone' => $transaction->customer_phone,
                    'customer_gender' => $transaction->customer_gender,
                    'customer_umroh_answer' => $transaction->customer_umroh_answer,
                    'custom_question_label' => $this->customQuestionLabel($transaction->event),
                    'event_name' => $transaction->event->name ?? '-',
                    'category_name' => $transaction->category->name ?? 'Mixed',
                    'quantity' => $transaction->quantity,
                    'total_amount' => $transaction->total_amount,
                    'payment_status' => $transaction->payment_status,
                    'payment_method' => $transaction->payment_method,
                    'proof_ig' => $proofData['proof_ig'] ?? null,
                    'proof_review' => $proofData['proof_review'] ?? null,
                    'proofs' => $proofData['proofs'] ?? [],
                    // Partial scan fields (gate_logs based)
                    'total_tickets'    => $totalTickets,
                    'redeemed_tickets' => $checkedInCount,
                    'is_partial_scan'  => $isPartialScan,
                ];
            })
            ->values();
    }

    private function buildTicketReportRows($transactions)
    {
        return $transactions
            ->flatMap(function ($transaction) {
                // Partial scan: based on gate_logs IN entries per ticket
                $nonVoidTickets   = $transaction->tickets->filter(fn ($t) => $t->status !== 'void');
                $totalTickets     = $nonVoidTickets->count();
                $checkedInCount   = $nonVoidTickets->filter(fn ($t) => $t->gateLogs->where('type', 'IN')->isNotEmpty())->count();
                $isPartialTxn     = $totalTickets > 1 && $checkedInCount > 0 && $checkedInCount < $totalTickets;

                return $transaction->tickets->map(function ($ticket) use ($transaction, $isPartialTxn, $totalTickets, $checkedInCount) {
                    $visitorData = is_array($ticket->visitor_data) ? $ticket->visitor_data : [];

                    // Gate scan data for this specific ticket
                    $inLogs        = $ticket->gateLogs->where('type', 'IN')->sortBy('scanned_at');
                    $hasCheckin    = $inLogs->isNotEmpty();
                    $firstCheckin  = $inLogs->first()?->scanned_at;

                    return [
                        'ticket_code' => $ticket->ticket_code,
                        'reference_no' => $transaction->reference_no,
                        'name' => $visitorData['name'] ?? $transaction->customer_name,
                        'nik' => $visitorData['nik'] ?? $transaction->customer_nik ?? '-',
                        'email' => $visitorData['email'] ?? $transaction->customer_email,
                        'phone' => $visitorData['phone'] ?? $transaction->customer_phone,
                        'gender' => $visitorData['gender'] ?? $transaction->customer_gender,
                        'umroh_answer' => $visitorData['umroh_answer'] ?? $transaction->customer_umroh_answer,
                        'custom_question_label' => $this->customQuestionLabel($transaction->event),
                        'event_name' => $transaction->event->name ?? '-',
                        'category_name' => $ticket->category->name ?? '-',
                        'status' => $ticket->status,
                        'redeemed_at' => $ticket->redeemed_at,
                        // Gate scan fields (accurate)
                        'has_checkin'       => $hasCheckin,
                        'first_checkin_at'  => $firstCheckin,
                        // Partial scan context
                        'is_partial_txn'    => $isPartialTxn,
                        'txn_total_tickets'  => $totalTickets,
                        'txn_redeemed'       => $checkedInCount,
                    ];
                });
            })
            ->values();
    }

    private function customQuestionLabel(?Event $event): string
    {
        if (!$event || !$event->umroh_question_enabled) {
            return '-';
        }

        $label = trim((string) ($event->meta['custom_question_text'] ?? ''));

        return $label !== '' ? $label : 'Pertanyaan Custom';
    }

    private function buildEventReport(Event $event): array
    {
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

        // Partial scan: use gate_logs (type=IN) as source of truth
        $partialScanByCategory = Transaction::query()
            ->where('event_id', $event->id)
            ->where('payment_status', 'paid')
            ->with(['tickets' => fn ($q) => $q->where('status', '!=', 'void')->with('gateLogs')])
            ->get()
            ->groupBy('ticket_category_id')
            ->map(function ($txns) {
                return $txns->filter(function ($txn) {
                    $nonVoid   = $txn->tickets;
                    $total     = $nonVoid->count();
                    $checkedIn = $nonVoid->filter(fn ($t) => $t->gateLogs->where('type', 'IN')->isNotEmpty())->count();
                    return $total > 1 && $checkedIn > 0 && $checkedIn < $total;
                })->count();
            });

        $categories = $event->ticketCategories->map(function ($category) use ($ticketStats, $gateStats, $transactionStats, $partialScanByCategory) {
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
                'partial_scan_count' => (int) ($partialScanByCategory->get($category->id) ?? 0),
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
            'partial_scan_count' => $categories->sum('partial_scan_count'),
        ];
    }
}
