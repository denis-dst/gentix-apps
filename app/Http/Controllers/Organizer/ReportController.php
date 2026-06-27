<?php

namespace App\Http\Controllers\Organizer;

use App\Exports\OperationalReportExport;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GateLog;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
            ->with(['event', 'category', 'tickets.category'])
            ->orderByDesc('created_at')
            ->get();

        $transactionReportRows = $this->buildTransactionReportRows($transactions);
        $ticketReportRows = $this->buildTicketReportRows($transactions);

        // Debug logging removed - not needed in production

        return view('organizer.reports.index', compact('events', 'eventOptions', 'reportRows', 'totals', 'transactions', 'transactionReportRows', 'ticketReportRows'));
    }

    public function duplicates(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $eventOptions = Event::where('tenant_id', $tenantId)
            ->orderByDesc('event_start_date')
            ->get(['id', 'name']);

        $transactions = Transaction::where('tenant_id', $tenantId)
            ->when($request->filled('event_id'), fn ($query) => $query->where('event_id', $request->event_id))
            ->with(['tickets'])
            ->orderByDesc('created_at')
            ->get();

        $duplicateRows = $this->buildDuplicateRegistrantRows($transactions);

        return view('organizer.reports.duplicates', compact('eventOptions', 'duplicateRows'));
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $tenantId = auth()->user()->tenant_id;

        $transactions = Transaction::where('tenant_id', $tenantId)
            ->when($request->filled('event_id'), fn ($query) => $query->where('event_id', $request->event_id))
            ->with(['event', 'category', 'tickets.category'])
            ->orderByDesc('created_at')
            ->get();

        $rows = $this->buildTicketReportRows($transactions)
            ->filter(fn (array $row) => ($row['status'] ?? '') !== 'void');

        $filename = 'Laporan_Pendaftar_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new OperationalReportExport($rows), $filename);
    }

    private function buildDuplicateRegistrantRows(Collection $transactions): Collection
    {
        $participants = $transactions->flatMap(function ($transaction) {
            return $transaction->tickets
                ->filter(fn ($ticket) => $ticket->status !== 'void')
                ->map(function ($ticket) use ($transaction) {
                    $visitorData = is_array($ticket->visitor_data) ? $ticket->visitor_data : [];

                    return [
                        'name' => trim((string) ($visitorData['name'] ?? $transaction->customer_name ?? '')),
                        'phone' => trim((string) ($visitorData['phone'] ?? $transaction->customer_phone ?? '')),
                        'email' => trim((string) ($visitorData['email'] ?? $transaction->customer_email ?? '')),
                    ];
                });
        })->filter(function ($row) {
            return $this->normalizePhone($row['phone'])
                || $this->normalizeEmail($row['email'])
                || $this->normalizeName($row['name']);
        });

        $phoneCounts = $participants
            ->map(fn ($row) => $this->normalizePhone($row['phone']))
            ->filter()
            ->countBy();
        $emailCounts = $participants
            ->map(fn ($row) => $this->normalizeEmail($row['email']))
            ->filter()
            ->countBy();
        $nameCounts = $participants
            ->map(fn ($row) => $this->normalizeName($row['name']))
            ->filter()
            ->countBy();

        $duplicateParticipants = $participants->filter(function ($row) use ($phoneCounts, $emailCounts, $nameCounts) {
            $phone = $this->normalizePhone($row['phone']);
            $email = $this->normalizeEmail($row['email']);
            $name = $this->normalizeName($row['name']);

            return ($phone && ($phoneCounts[$phone] ?? 0) > 1)
                || ($email && ($emailCounts[$email] ?? 0) > 1)
                || ($name && ($nameCounts[$name] ?? 0) > 1);
        });

        return $duplicateParticipants
            ->groupBy(function ($row) {
                return $this->normalizeName($row['name']) . '|' . $this->normalizePhone($row['phone']) . '|' . $this->normalizeEmail($row['email']);
            })
            ->map(function ($group) {
                $first = $group->first();

                return [
                    'name' => $first['name'] ?: '-',
                    'phone' => $first['phone'] ?: '-',
                    'email' => $first['email'] ?: '-',
                    'registration_count' => $group->count(),
                ];
            })
            ->filter(function ($row) use ($phoneCounts, $emailCounts, $nameCounts) {
                $phone = $this->normalizePhone($row['phone']);
                $email = $this->normalizeEmail($row['email']);
                $name = $this->normalizeName($row['name']);

                return ($phone && ($phoneCounts[$phone] ?? 0) > 1)
                    || ($email && ($emailCounts[$email] ?? 0) > 1)
                    || ($name && ($nameCounts[$name] ?? 0) > 1);
            })
            ->sortByDesc('registration_count')
            ->values();
    }

    private function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (!str_starts_with($digits, '62')) {
            return '62' . $digits;
        }

        return $digits;
    }

    private function normalizeEmail(string $email): ?string
    {
        $normalized = strtolower(trim($email));

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizeName(string $name): ?string
    {
        $normalized = mb_strtolower(trim(preg_replace('/\s+/', ' ', $name) ?? ''));

        return $normalized !== '' ? $normalized : null;
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
                ];
            })
            ->values();
    }

    private function buildTicketReportRows($transactions)
    {
        return $transactions
            ->flatMap(function ($transaction) {
                return $transaction->tickets->map(function ($ticket) use ($transaction) {
                    $visitorData = is_array($ticket->visitor_data) ? $ticket->visitor_data : [];

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
