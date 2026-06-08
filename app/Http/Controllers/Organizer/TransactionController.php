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
        
        Mail::to($transaction->customer_email)->send(new \App\Mail\EVoucherMail($transaction));
        
        return back()->with('success', 'E-Voucher berhasil dikirim ulang ke ' . $transaction->customer_email);
    }

    public function printEvoucher(Transaction $transaction)
    {
        $transaction->load(['event', 'tickets.category', 'tenant']);
        return view('organizer.transactions.evoucher', compact('transaction'));
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
            'payment_method' => $transaction->payment_method ?? 'MANUAL_VERIFICATION'
        ]);

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

        return back()->with('success', 'Tiket ' . $ticket->ticket_code . ' berhasil dibatalkan dan kuota telah dikembalikan.');
    }

    public function cancelTransaction(Transaction $transaction)
    {
        $this->authorizeTenant($transaction->event);

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

            $transaction->update([
                'quantity' => 0,
                'total_amount' => 0,
                'payment_status' => 'refunded'
            ]);
        });

        return back()->with('success', 'Transaksi #' . $transaction->reference_no . ' berhasil dibatalkan sepenuhnya dan semua kuota telah dikembalikan.');
    }

    private function authorizeTenant(Event $event)
    {
        if ($event->tenant_id !== auth()->user()->tenant_id && !auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Unauthorized access to this event');
        }
    }
}
