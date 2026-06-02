<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Event;
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

    public function cancel(Transaction $transaction)
    {
        $tenantId = auth()->user()->tenant_id;
        if ($transaction->tenant_id !== $tenantId) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($transaction->payment_status, ['failed', 'expired', 'refunded'])) {
            return back()->with('error', 'Transaksi sudah berstatus tidak aktif (failed/expired/refunded).');
        }

        $wasPaid = $transaction->payment_status === 'paid';

        \Illuminate\Support\Facades\DB::transaction(function() use ($transaction, $wasPaid) {
            // Update transaction status
            $transaction->update([
                'payment_status' => 'failed', // Mark as failed/cancelled
            ]);

            // Restore quota (decrement sold_count) only if it was paid
            if ($wasPaid) {
                $category = $transaction->category;
                if ($category) {
                    $category->decrement('sold_count', $transaction->quantity);
                }

                // Soft delete the tickets associated with this transaction
                $transaction->tickets()->delete();
            }

            // Restore promo code usage if any
            if ($transaction->promoCode) {
                $transaction->promoCode->decrement('used_count');
            }
        });

        return back()->with('success', 'Transaksi #' . $transaction->reference_no . ' berhasil dicancel dan kuota telah dikembalikan.');
    }
}
