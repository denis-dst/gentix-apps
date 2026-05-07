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
        $transaction->load(['tickets.event', 'tickets.category']);
        
        foreach ($transaction->tickets as $ticket) {
            Mail::to($transaction->customer_email)->send(new \App\Mail\EVoucherMail($ticket));
        }
        
        return back()->with('success', 'E-Voucher berhasil dikirim ulang ke ' . $transaction->customer_email);
    }

    public function printEvoucher(Transaction $transaction)
    {
        $transaction->load(['event', 'tickets.category', 'tenant']);
        return view('organizer.transactions.evoucher', compact('transaction'));
    }
}
