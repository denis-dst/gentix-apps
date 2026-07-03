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

    public function downloadData(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $eventId = (int) $request->event_id;
        $event = Event::findOrFail($eventId);

        $tickets = Ticket::query()
            ->leftJoin('ticket_categories', 'ticket_categories.id', '=', 'tickets.ticket_category_id')
            ->leftJoin('transactions', 'transactions.id', '=', 'tickets.transaction_id')
            ->where('tickets.event_id', $eventId)
            ->whereIn('status', ['sold', 'redeemed'])
            ->select([
                'tickets.id as ticket_id',
                'tickets.event_id',
                'tickets.tenant_id',
                'tickets.ticket_category_id',
                'tickets.ticket_code',
                'tickets.wristband_qr',
                'tickets.visitor_data',
                'ticket_categories.name as category_name',
                'transactions.customer_name',
                'transactions.customer_email',
                'transactions.customer_umroh_answer',
                'transactions.reference_no',
            ])
            ->get()
            ->map(function ($ticket) use ($event) {
                $visitorData = $this->visitorDataArray($ticket->visitor_data);
                $customQuestion = $this->customQuestionPayload($event, $visitorData, $ticket->customer_umroh_answer);

                return [
                    'ticket_id' => $ticket->ticket_id,
                    'event_id' => $ticket->event_id,
                    'tenant_id' => $ticket->tenant_id,
                    'ticket_category_id' => $ticket->ticket_category_id,
                    'ticket_code' => $ticket->ticket_code,
                    'wristband_qr' => $ticket->wristband_qr,
                    'category_name' => $ticket->category_name ?? '-',
                    'customer_name' => $visitorData['name'] ?? $ticket->customer_name ?? '-',
                    'customer_email' => $ticket->customer_email ?? '-',
                    'custom_question_label' => $customQuestion['label'],
                    'custom_question_answer' => $customQuestion['answer'],
                    'custom_question' => $customQuestion,
                    'reference_no' => $ticket->reference_no ?? '-',
                ];
            })
            ->values();

        $gates = Gate::where('event_id', $eventId)
            ->where('is_active', true)
            ->with(['ticketCategories' => function ($query) {
                $query->select('ticket_categories.id', 'name');
            }])
            ->get()
            ->map(function ($gate) {
                return [
                    'gate_id' => $gate->id,
                    'event_id' => $gate->event_id,
                    'gate_name' => $gate->name,
                    'allowed_category_ids' => $gate->ticketCategories->pluck('id')->values(),
                ];
            })
            ->values();

        return response()->json([
            'status' => 'SUCCESS',
            'event_id' => $eventId,
            'tickets' => $tickets,
            'gates' => $gates,
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

        $ticket = Ticket::with(['category', 'transaction', 'event'])
            ->where('wristband_qr', $scanCode)
            ->orWhere('ticket_code', $scanCode)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'REJECT',
                'message' => 'Invalid Wristband / Ticket Code',
                'color' => 'pink',
                'ticket_code' => $scanCode,
                'category' => '-',
                'email' => '-',
                'reference_no' => '-',
            ], 404);
        }

        if (($ticket->event->purchase_flow ?? 'redeem') === 'redeem' && $ticket->status !== 'redeemed') {
            return response()->json([
                'status' => 'REJECT',
                'message' => 'Tiket belum diredeem menjadi wristband.',
                'color' => 'pink',
                'visitor' => $ticket->transaction->customer_name ?? '-',
                'category' => $ticket->category->name ?? '-',
                'ticket_code' => $ticket->ticket_code,
                'email' => $ticket->transaction->customer_email ?? '-',
                'reference_no' => $ticket->transaction->reference_no ?? '-',
            ], 403);
        }

        if ($ticket->status === 'void') {
            return response()->json([
                'status' => 'REJECT',
                'message' => 'Tiket sudah dibatalkan.',
                'color' => 'pink',
                'visitor' => $ticket->transaction->customer_name ?? '-',
                'category' => $ticket->category->name ?? '-',
                'ticket_code' => $ticket->ticket_code,
                'email' => $ticket->transaction->customer_email ?? '-',
                'reference_no' => $ticket->transaction->reference_no ?? '-',
            ], 403);
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
                        'category' => $ticket->category->name,
                        'ticket_code' => $ticket->ticket_code,
                        'email' => $ticket->transaction->customer_email ?? '-',
                        'reference_no' => $ticket->transaction->reference_no ?? '-',
                    ], 403);
                }
                // Use gate name from the database if gate_id is provided
                $gateName = $gate->name;
            }
        } else {
            $gateName = $request->gate_name;
        }

        // For group checking (transactions with quantity > 1), we return the group details
        $transaction = $ticket->transaction;
        if ($transaction && $transaction->tickets()->count() > 1) {
            $ticketsInGroup = $transaction->tickets()->with(['category', 'gateLogs' => function($q) {
                $q->with('scanner')->orderBy('scanned_at', 'desc');
            }])->get();

            $ticketsInGroup->loadMissing('event');

            $totalGroupCount = $ticketsInGroup->count();
            $checkedInCount = 0;
            $checkedOutCount = 0;
            $neverCheckedInCount = 0;
            $latestLog = null;

            foreach ($ticketsInGroup as $t) {
                $tLastLog = $t->gateLogs->first();
                if ($tLastLog) {
                    if ($tLastLog->type === 'IN') {
                        $checkedInCount++;
                    } else {
                        $checkedOutCount++;
                    }
                    if (!$latestLog || ($tLastLog->scanned_at && $latestLog->scanned_at && $tLastLog->scanned_at->gt($latestLog->scanned_at))) {
                        $latestLog = $tLastLog;
                    }
                } else {
                    $neverCheckedInCount++;
                    $checkedOutCount++;
                }
            }

            if ($request->type === 'IN') {
                if ($checkedInCount === $totalGroupCount) {
                    $operatorName = $latestLog && $latestLog->scanner ? $latestLog->scanner->name : ($latestLog->gate_name ?? 'System');
                    $timeString = $latestLog && $latestLog->scanned_at ? $latestLog->scanned_at->timezone('Asia/Jakarta')->format('H:i:s d-m-Y') : '-';
                    return response()->json([
                        'status' => 'REJECT',
                        'message' => "Seluruh peserta ({$totalGroupCount} orang) sudah Checkin. Checkin terakhir pada {$timeString} oleh Operator {$operatorName}.",
                        'color' => 'pink',
                        'visitor' => $transaction->customer_name ?? '-',
                        'category' => $ticket->category->name,
                        'ticket_code' => $ticket->ticket_code,
                        'email' => $transaction->customer_email ?? '-',
                        'reference_no' => $transaction->reference_no ?? '-',
                    ], 403);
                }
            } else {
                if ($checkedOutCount === $totalGroupCount) {
                    $message = $neverCheckedInCount === $totalGroupCount
                        ? "Seluruh peserta ({$totalGroupCount} orang) belum pernah Check-in!"
                        : "Seluruh peserta ({$totalGroupCount} orang) sudah berada di luar area!";
                    return response()->json([
                        'status' => 'REJECT',
                        'message' => $message,
                        'color' => 'pink',
                        'visitor' => $transaction->customer_name ?? '-',
                        'category' => $ticket->category->name,
                        'ticket_code' => $ticket->ticket_code,
                        'email' => $transaction->customer_email ?? '-',
                        'reference_no' => $transaction->reference_no ?? '-',
                    ], 403);
                }
            }

            $attendeesList = $ticketsInGroup->map(function($t) {
                $lastLog = $t->gateLogs->first();
                $isCheckedIn = $lastLog && $lastLog->type === 'IN';
                $visitorData = $this->visitorDataArray($t->visitor_data);
                $customQuestion = $this->ticketCustomQuestionPayload($t);

                return [
                    'ticket_id' => $t->id,
                    'ticket_category_id' => $t->ticket_category_id,
                    'ticket_code' => $t->ticket_code,
                    'name' => $visitorData['name'] ?? $t->transaction->customer_name,
                    'gender' => $visitorData['gender'] ?? null,
                    'custom_question_label' => $customQuestion['label'],
                    'custom_question_answer' => $customQuestion['answer'],
                    'custom_question' => $customQuestion,
                    'is_checked_in' => $isCheckedIn,
                    'category' => $t->category->name,
                    'checked_in_at' => ($isCheckedIn && $lastLog->scanned_at) ? $lastLog->scanned_at->timezone('Asia/Jakarta')->format('H:i:s d-m-Y') : null,
                    'checked_in_by' => ($isCheckedIn && $lastLog->scanner) ? $lastLog->scanner->name : ($lastLog->gate_name ?? null),
                ];
            });

            $customQuestion = $this->ticketCustomQuestionPayload($ticket);

            return response()->json([
                'status' => 'SUCCESS',
                'is_group' => true,
                'message' => 'Detail grup ditemukan',
                'visitor' => $transaction->customer_name ?? '-',
                'category' => $ticket->category->name,
                'attendees' => $attendeesList,
                'custom_question_label' => $customQuestion['label'],
                'custom_question_answer' => $customQuestion['answer'],
                'custom_question' => $customQuestion,
                'scanned_ticket_id' => $ticket->id,
                'ticket_code' => $ticket->ticket_code,
                'email' => $transaction->customer_email ?? '-',
                'reference_no' => $transaction->reference_no ?? '-',
            ]);
        }

        // Anti-passback: the movement must alternate IN -> OUT -> IN -> OUT.
        $lastLog = GateLog::where('ticket_id', $ticket->id)
            ->with('scanner')
            ->orderBy('scanned_at', 'desc')
            ->first();

        $alreadyInState = false;
        if ($request->type === 'IN') {
            if ($lastLog && $lastLog->type === 'IN') {
                $alreadyInState = true;
            }
        } else {
            if ($lastLog && $lastLog->type === 'OUT') {
                $alreadyInState = true;
            }
        }

        if ($alreadyInState) {
            $operatorName = $lastLog->scanner ? $lastLog->scanner->name : ($lastLog->gate_name ?? 'System');
            $timeString = $lastLog->scanned_at ? $lastLog->scanned_at->timezone('Asia/Jakarta')->format('H:i:s d-m-Y') : '-';
            $visitorData = $this->visitorDataArray($ticket->visitor_data);
            $visitorName = $visitorData['name'] ?? ($ticket->transaction->customer_name ?? '-');
            
            $actionText = $request->type === 'IN' ? 'Checkin' : 'Checkout';
            return response()->json([
                'status' => 'REJECT',
                'message' => "Sudah {$actionText} pada waktu {$timeString} oleh Operator {$operatorName} dengan QR {$ticket->ticket_code} atas nama {$visitorName}",
                'color' => 'pink',
                'visitor' => $visitorName,
                'category' => $ticket->category->name,
                'ticket_code' => $ticket->ticket_code,
                'email' => $ticket->transaction->customer_email ?? '-',
                'reference_no' => $ticket->transaction->reference_no ?? '-',
            ], 403);
        }

        if ($request->type === 'OUT' && (!$lastLog || $lastLog->type !== 'IN')) {
            $visitorData = $this->visitorDataArray($ticket->visitor_data);
            $visitorName = $visitorData['name'] ?? ($ticket->transaction->customer_name ?? '-');
            return response()->json([
                'status' => 'REJECT',
                'message' => $lastLog && $lastLog->type === 'OUT'
                    ? 'Tiket sudah berada di luar area!'
                    : 'Tiket belum pernah Check-in!',
                'color' => 'pink',
                'visitor' => $visitorName,
                'category' => $ticket->category->name,
                'ticket_code' => $ticket->ticket_code,
                'email' => $ticket->transaction->customer_email ?? '-',
                'reference_no' => $ticket->transaction->reference_no ?? '-',
            ], 403);
        }

        // Log the movement
        $visitorData = $this->visitorDataArray($ticket->visitor_data);
        $customQuestion = $this->ticketCustomQuestionPayload($ticket);

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
            'visitor' => $visitorData['name'] ?? $ticket->transaction->customer_name ?? '-',
            'category' => $ticket->category->name,
            'color' => 'green',
            'ticket_code' => $ticket->ticket_code,
            'email' => $ticket->transaction->customer_email ?? '-',
            'custom_question_label' => $customQuestion['label'],
            'custom_question_answer' => $customQuestion['answer'],
            'custom_question' => $customQuestion,
            'reference_no' => $ticket->transaction->reference_no ?? '-',
        ]);
    }

    /**
     * Background Sync (Hibrida Offline-First)
     */
    public function syncLogs(Request $request)
    {
        $request->validate([
            'logs' => 'required|array',
        ]);

        $logs = $request->input('logs', []);
        
        foreach ($logs as $logData) {
            if (empty($logData['offline_id']) || empty($logData['ticket_id']) || empty($logData['type'])) {
                continue;
            }

            GateLog::updateOrCreate(
                ['meta->offline_id' => $logData['offline_id']],
                [
                    'tenant_id' => $logData['tenant_id'],
                    'event_id' => $logData['event_id'],
                    'ticket_id' => $logData['ticket_id'],
                    'gate_name' => $logData['gate_name'],
                    'type' => $logData['type'],
                    'scanned_at' => $logData['scanned_at'],
                    'device_id' => $logData['device_id'] ?? null,
                    'scanned_by' => auth()->id(),
                    'meta' => [
                        'offline_id' => $logData['offline_id'],
                        'synced_at' => now(),
                    ],
                ]
            );
        }

        return response()->json(['message' => count($logs) . ' logs synced successfully']);
    }

    /**
     * Bulk Check-in/Check-out for Group Scan
     */
    public function bulkCheckin(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id',
            'type' => 'required|in:IN,OUT',
            'gate_id' => 'nullable|exists:gates,id',
            'gate_name' => 'required|string',
            'device_id' => 'nullable'
        ]);

        try {
            $processedTickets = [];
            \Illuminate\Support\Facades\DB::transaction(function() use ($request, &$processedTickets) {
                $allowedCategoryIds = [];

                if ($request->gate_id) {
                    $gate = Gate::with('ticketCategories')->find($request->gate_id);
                    $allowedCategoryIds = $gate
                        ? $gate->ticketCategories->pluck('id')->map(fn ($id) => (int) $id)->all()
                        : [];
                }

                foreach ($request->ticket_ids as $ticketId) {
                    $ticket = Ticket::with(['category', 'transaction', 'event'])->findOrFail($ticketId);

                    if (!empty($allowedCategoryIds) && !in_array((int) $ticket->ticket_category_id, $allowedCategoryIds, true)) {
                        continue;
                    }
                    
                    // Check logic status first to prevent duplicate active state
                    $lastLog = GateLog::where('ticket_id', $ticketId)
                        ->orderBy('scanned_at', 'desc')
                        ->first();

                    $alreadyInState = $lastLog && $lastLog->type === $request->type;

                    if ($request->type === 'IN') {
                        if ($alreadyInState) continue;
                    } else {
                        $hasIn = GateLog::where('ticket_id', $ticketId)->where('type', 'IN')->exists();
                        if (!$hasIn || $alreadyInState) continue;
                    }

                    GateLog::create([
                        'tenant_id' => $ticket->tenant_id,
                        'event_id' => $ticket->event_id,
                        'ticket_id' => $ticketId,
                        'gate_name' => $request->gate_name,
                        'type' => $request->type,
                        'scanned_at' => now(),
                        'device_id' => $request->device_id,
                        'scanned_by' => auth()->id()
                    ]);

                    $processedTickets[] = $ticket;
                }
            });

            if (empty($processedTickets)) {
                $firstId = reset($request->ticket_ids);
                $firstTicket = Ticket::with(['category', 'transaction', 'event'])->find($firstId);
                if ($firstTicket) {
                    $processedTickets[] = $firstTicket;
                }
            }

            if (!empty($processedTickets)) {
                $firstTicket = $processedTickets[0];
                $transaction = $firstTicket->transaction;

                $visitorNames = [];
                $ticketCodes = [];
                $customQuestions = [];

                foreach ($processedTickets as $t) {
                    $visitorData = $this->visitorDataArray($t->visitor_data);
                    $visitorNames[] = $visitorData['name'] ?? ($transaction->customer_name ?? '-');
                    $ticketCodes[] = $t->ticket_code;

                    $customQ = $this->ticketCustomQuestionPayload($t);
                    if ($customQ['label'] !== '-' && $customQ['answer'] !== '-') {
                        $customQuestions[] = $customQ['label'] . ': ' . $customQ['answer'];
                    }
                }

                $visitorNameString = implode(', ', array_unique($visitorNames));
                $ticketCodeString = implode(', ', array_unique($ticketCodes));
                $customQAnswerString = implode('; ', array_unique($customQuestions));

                return response()->json([
                    'status' => 'SUCCESS',
                    'message' => 'Berhasil memproses check-in masal',
                    'visitor' => $visitorNameString,
                    'category' => $firstTicket->category->name,
                    'ticket_code' => $ticketCodeString,
                    'email' => $transaction->customer_email ?? '-',
                    'reference_no' => $transaction->reference_no ?? '-',
                    'custom_question_label' => count($customQuestions) > 0 ? 'Pertanyaan Custom' : '-',
                    'custom_question_answer' => count($customQuestions) > 0 ? $customQAnswerString : '-',
                ]);
            }

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Berhasil memproses check-in masal',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function ticketCustomQuestionPayload(Ticket $ticket): array
    {
        $visitorData = $this->visitorDataArray($ticket->visitor_data);

        return $this->customQuestionPayload(
            $ticket->event,
            $visitorData,
            $ticket->transaction->customer_umroh_answer ?? null
        );
    }

    private function customQuestionPayload(?Event $event, array $visitorData, ?string $fallbackAnswer = null): array
    {
        $label = '-';

        if ($event && $event->umroh_question_enabled) {
            $eventLabel = trim((string) ($event->meta['custom_question_text'] ?? ''));
            $label = $eventLabel !== '' ? $eventLabel : 'Pertanyaan Custom';
        }

        $answer = $visitorData['umroh_answer'] ?? $fallbackAnswer;

        return [
            'label' => $label,
            'answer' => $answer ?: '-',
        ];
    }

    private function visitorDataArray(mixed $visitorData): array
    {
        if (is_array($visitorData)) {
            return $visitorData;
        }

        if (is_string($visitorData) && $visitorData !== '') {
            $decoded = json_decode($visitorData, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
