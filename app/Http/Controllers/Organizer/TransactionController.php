<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = Transaction::where('tenant_id', $tenantId)
            ->with(['event', 'tickets.category'])
            ->orderByDesc('created_at');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($query) use ($q) {
                $query->where('customer_email', 'like', "%$q%")
                    ->orWhere('customer_name', 'like', "%$q%")
                    ->orWhere('reference_no', 'like', "%$q%");
            });
        }

        if ($request->filled('ticket_category_id')) {
            $query->where('ticket_category_id', $request->ticket_category_id);
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $transactions = $query->paginate(20)->withQueryString();

        $eventOptions = Event::where('tenant_id', $tenantId)->orderByDesc('created_at')->get(['id', 'name']);
        $ticketCategories = TicketCategory::whereHas('event', function($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
        })->get(['id', 'name']);

        return view('organizer.transactions.index', compact('transactions', 'eventOptions', 'ticketCategories'));
    }

    public function resendEvoucher(Transaction $transaction)
    {
        $transaction->load(['event', 'tickets.category']);

        try {
            Mail::to($transaction->customer_email)->send(new \App\Mail\EVoucherMail($transaction));
        } catch (\Exception $e) {
            \Log::error('resendEvoucher failed for transaction #' . $transaction->reference_no . ': ' . $e->getMessage(), [
                'userId' => auth()->id(),
                'email'  => $transaction->customer_email,
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim e-voucher: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal mengirim e-voucher ke ' . $transaction->customer_email . '. Silakan coba lagi atau hubungi administrator. Detail: ' . $e->getMessage());
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'E-Voucher berhasil dikirim ulang ke ' . $transaction->customer_email,
            ]);
        }

        return back()->with('success', 'E-Voucher berhasil dikirim ulang ke ' . $transaction->customer_email);
    }

    public function printEvoucher(Transaction $transaction)
    {
        $transaction->load(['event', 'tickets']);

        $this->authorizeTenant($transaction->event);

        $activeTicket = $transaction->tickets->first(fn ($ticket) => $ticket->status !== 'void');

        if (!$activeTicket) {
            return back()->with('error', 'Tidak ada tiket aktif untuk dicetak.');
        }

        return redirect()->route('tickets.view', $activeTicket->ticket_code);
    }

    public function markAsPaid(Transaction $transaction)
    {
        if ($transaction->payment_status === 'paid') {
            return back()->with('error', 'Transaksi sudah berstatus PAID.');
        }

        \Illuminate\Support\Facades\DB::transaction(function() use ($transaction) {
            $transaction->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'processed_by' => auth()->id(),
                'payment_method' => $transaction->payment_method ?? 'MANUAL_VERIFICATION'
            ]);

            $category = $transaction->category;
            
            // Check if tickets already exist to prevent duplicate generation
            if ($transaction->tickets()->count() === 0) {
                for ($i = 0; $i < $transaction->quantity; $i++) {
                    $ticket = \App\Models\Ticket::create([
                        'tenant_id' => $transaction->tenant_id,
                        'event_id' => $transaction->event_id,
                        'transaction_id' => $transaction->id,
                        'ticket_category_id' => $transaction->ticket_category_id,
                        'ticket_code' => 'GTX-' . strtoupper(\Illuminate\Support\Str::random(10)),
                        'status' => 'sold',
                    ]);

                    // Send Notification
                    try {
                        $notificationService = new \App\Services\TicketNotificationService();
                        $notificationService->sendEVoucher($ticket);
                    } catch (\Exception $e) {
                        // Ignore mail/WA error in admin panel
                    }
                }

                if ($category) {
                    $category->increment('sold_count', $transaction->quantity);
                }
            }
        });

        return back()->with('success', 'Transaksi #' . $transaction->reference_no . ' telah dikonfirmasi lunas.');
    }

    public function cancelTicket(Ticket $ticket)
    {
        $this->authorizeTenant($ticket->event);

        if ($ticket->status === 'void') {
            return back()->with('error', 'Tiket ini sudah dibatalkan sebelumnya.');
        }

        \DB::transaction(function() use ($ticket) {
            $ticket->update(['status' => 'void']);

            if ($ticket->category) {
                $ticket->category->decrement('sold_count', 1);
            }

            $transaction = $ticket->transaction;
            $oldQty = $transaction->quantity;
            $newQty = max(0, $oldQty - 1);

            $pricePerTicket = $oldQty > 0 ? ($transaction->total_amount / $oldQty) : 0;
            $newAmount = max(0, $transaction->total_amount - $pricePerTicket);

            $transaction->update([
                'quantity' => $newQty,
                'total_amount' => $newAmount,
                'payment_status' => $newQty === 0 ? 'refunded' : $transaction->payment_status
            ]);
        });

        $activeTicket = $ticket->transaction->tickets()
            ->where('status', '!=', 'void')
            ->latest('id')
            ->first();

        $redirect = back()->with('success', 'Tiket ' . $ticket->ticket_code . ' berhasil dibatalkan dan kuota telah dikembalikan.');

        if ($activeTicket) {
            $redirect->with('active_evoucher_url', route('tickets.view', $activeTicket->ticket_code));
        }

        return $redirect;
    }

    public function cancelTransaction(Transaction $transaction)
    {
        $this->authorizeTenant($transaction->event);

        if (in_array($transaction->payment_status, ['failed', 'expired', 'refunded'])) {
            return back()->with('error', 'Transaksi sudah berstatus tidak aktif atau dibatalkan sebelumnya.');
        }

        \DB::transaction(function() use ($transaction) {
            $activeTickets = $transaction->tickets()->where('status', '!=', 'void')->get();

            foreach ($activeTickets as $ticket) {
                $ticket->update(['status' => 'void']);
                if ($ticket->category) {
                    $ticket->category->decrement('sold_count', 1);
                }
            }

            // Restore promo code usage if any
            if ($transaction->promoCode) {
                $transaction->promoCode->decrement('used_count');
            }

            $transaction->update([
                'quantity' => 0,
                'total_amount' => 0,
                'payment_status' => $transaction->payment_status === 'paid' ? 'refunded' : 'failed'
            ]);
        });

        return back()->with('success', 'Transaksi #' . $transaction->reference_no . ' berhasil dibatalkan sepenuhnya dan semua kuota telah dikembalikan.');
    }

    public function cancelTickets(Request $request, Transaction $transaction)
    {
        $this->authorizeTenant($transaction->event);

        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:tickets,id'
        ]);

        $ticketIds = $request->input('ticket_ids');

        $ticketsToCancel = $transaction->tickets()
            ->whereIn('id', $ticketIds)
            ->where('status', '!=', 'void')
            ->get();

        if ($ticketsToCancel->isEmpty()) {
            return back()->with('error', 'Tidak ada tiket aktif terpilih untuk dibatalkan.');
        }

        \DB::transaction(function() use ($transaction, $ticketsToCancel) {
            $numCanceled = $ticketsToCancel->count();

            foreach ($ticketsToCancel as $ticket) {
                $ticket->update(['status' => 'void']);
                if ($ticket->category) {
                    $ticket->category->decrement('sold_count', 1);
                }
            }

            $oldQty = $transaction->quantity;
            $newQty = max(0, $oldQty - $numCanceled);

            $pricePerTicket = $oldQty > 0 ? ($transaction->total_amount / $oldQty) : 0;
            $refundAmount = $pricePerTicket * $numCanceled;
            $newAmount = max(0, $transaction->total_amount - $refundAmount);

            $updateData = [
                'quantity' => $newQty,
                'total_amount' => $newAmount,
            ];

            if ($newQty === 0) {
                $updateData['payment_status'] = $transaction->payment_status === 'paid' ? 'refunded' : 'failed';
                if ($transaction->promoCode) {
                    $transaction->promoCode->decrement('used_count');
                }
            }

            $transaction->update($updateData);
        });

        $activeTicket = $transaction->tickets()
            ->where('status', '!=', 'void')
            ->latest('id')
            ->first();

        $redirect = back()->with('success', 'Tiket terpilih (' . $ticketsToCancel->count() . ' tiket) berhasil dibatalkan dan kuota telah dikembalikan.');

        if ($activeTicket) {
            $redirect->with('active_evoucher_url', route('tickets.view', $activeTicket->ticket_code));
        }

        return $redirect;
    }

    public function sendWhatsApp(Transaction $transaction)
    {
        $this->authorizeTenant($transaction->event);
        $transaction->load(['event', 'tickets.category']);

        $ticket = $transaction->tickets->first(fn ($t) => $t->status !== 'void');

        if (!$ticket) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada tiket aktif untuk mengirim WhatsApp.',
                ], 404);
            }
            return back()->with('error', 'Tidak ada tiket aktif untuk mengirim WhatsApp.');
        }

        try {
            $notificationService = new \App\Services\TicketNotificationService();
            $notificationService->sendWhatsApp($ticket, true, true);
        } catch (\Exception $e) {
            \Log::error('sendWhatsApp failed for transaction #' . $transaction->reference_no . ': ' . $e->getMessage(), [
                'userId' => auth()->id(),
            ]);

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim WhatsApp: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal mengirim WhatsApp. Detail: ' . $e->getMessage());
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'E-Voucher berhasil dikirim via WhatsApp Fonnte.',
            ]);
        }

        return back()->with('success', 'E-Voucher berhasil dikirim via WhatsApp Fonnte.');
    }

    private function authorizeTenant(Event $event)
    {
        if ($event->tenant_id !== auth()->user()->tenant_id && !auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Unauthorized access to this event');
        }
    }
}
