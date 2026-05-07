<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\Tenant;
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
