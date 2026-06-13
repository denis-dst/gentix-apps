<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['event', 'tickets.category', 'tenant'])
            ->orderByDesc('created_at');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($query) use ($q) {
                $query->where('customer_email', 'like', "%$q%")
                    ->orWhere('customer_name', 'like', "%$q%")
                    ->orWhere('reference_no', 'like', "%$q%");
            });
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('ticket_category_id')) {
            $query->where('ticket_category_id', $request->ticket_category_id);
        }

        $transactions = $query->paginate(20)->withQueryString();

        $tenantOptions = Tenant::orderBy('name')->get(['id', 'name']);
        $eventOptions = Event::when($request->filled('tenant_id'), fn($q) => $q->where('tenant_id', $request->tenant_id))
            ->orderByDesc('created_at')
            ->get(['id', 'name']);
        
        $ticketCategories = TicketCategory::when($request->filled('event_id'), fn($q) => $q->where('event_id', $request->event_id))
            ->get(['id', 'name']);

        return view('superadmin.transactions.index', compact('transactions', 'tenantOptions', 'eventOptions', 'ticketCategories'));
    }

    public function resendEvoucher(Transaction $transaction)
    {
        $transaction->load(['event', 'tickets.category']);
        
        Mail::to($transaction->customer_email)->send(new \App\Mail\EVoucherMail($transaction));
        
        return back()->with('success', 'E-Voucher berhasil dikirim ulang ke ' . $transaction->customer_email);
    }

    public function printEvoucher(Transaction $transaction)
    {
        $transaction->load(['event', 'tickets']);

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

        $transaction->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
            'processed_by' => auth()->id(),
            'payment_method' => $transaction->payment_method ?? 'MANUAL_VERIFICATION_SUPERADMIN'
        ]);

        return back()->with('success', 'Transaksi #' . $transaction->reference_no . ' telah dikonfirmasi lunas oleh SuperAdmin.');
    }

    public function cancelTicket(Ticket $ticket)
    {
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

        $redirect = back()->with('success', 'Tiket ' . $ticket->ticket_code . ' berhasil dibatalkan dan kuota telah dikembalikan oleh SuperAdmin.');

        if ($activeTicket) {
            $redirect->with('active_evoucher_url', route('tickets.view', $activeTicket->ticket_code));
        }

        return $redirect;
    }

    public function cancelTransaction(Transaction $transaction)
    {
        if ($transaction->payment_status === 'refunded') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
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
                'payment_status' => 'refunded'
            ]);
        });

        return back()->with('success', 'Transaksi #' . $transaction->reference_no . ' berhasil dibatalkan sepenuhnya dan semua kuota telah dikembalikan oleh SuperAdmin.');
    }

    public function cancelTickets(Request $request, Transaction $transaction)
    {
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

        $redirect = back()->with('success', 'Tiket terpilih (' . $ticketsToCancel->count() . ' tiket) berhasil dibatalkan dan kuota telah dikembalikan oleh SuperAdmin.');

        if ($activeTicket) {
            $redirect->with('active_evoucher_url', route('tickets.view', $activeTicket->ticket_code));
        }

        return $redirect;
    }
}
