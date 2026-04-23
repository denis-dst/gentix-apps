<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\Transaction;
use App\Services\TicketNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicEventController extends Controller
{
    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->with(['ticketCategories' => function($query) {
                $query->where('is_active', true)->orderBy('price', 'asc');
            }])
            ->firstOrFail();

        return view('events.show', compact('event'));
    }

    public function checkout(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        
        $validated = $request->validate([
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'quantity' => 'required|integer|min:1|max:2', // Limit 2 tickets per transaction
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'nik' => 'required|string|size:16',
        ]);

        $category = TicketCategory::findOrFail($validated['ticket_category_id']);
        
        // Availability Check
        if ($category->sold_count + $validated['quantity'] > $category->quota) {
            return back()->with('error', 'Sorry, requested quantity exceeds available quota.');
        }

        // Time Check
        $now = now();
        if ($category->sale_start_at && $now->lt($category->sale_start_at)) {
            return back()->with('error', 'Tickets for this category are not yet available.');
        }
        if ($category->sale_end_at && $now->gt($category->sale_end_at)) {
            return back()->with('error', 'Ticket sales for this category have ended.');
        }

        return DB::transaction(function () use ($event, $category, $validated) {
            $totalAmount = $category->price * $validated['quantity'];
            $referenceNo = 'TX-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $transaction = Transaction::create([
                'tenant_id' => $event->tenant_id,
                'event_id' => $event->id,
                'reference_no' => $referenceNo,
                'customer_name' => $validated['name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'],
                'customer_nik' => $validated['nik'],
                'total_amount' => $totalAmount,
                'payment_status' => 'paid', // Simulating instant payment
                'payment_method' => 'Bank Transfer',
                'channel' => 'online',
                'paid_at' => now(),
            ]);

            for ($i = 0; $i < $validated['quantity']; $i++) {
                $ticket = Ticket::create([
                    'transaction_id' => $transaction->id,
                    'ticket_category_id' => $category->id,
                    'ticket_code' => 'GTX-' . strtoupper(Str::random(10)),
                    'status' => 'active',
                ]);

                // Trigger Notification (Email/WA)
                (new TicketNotificationService())->sendTicketNotification($ticket);
            }

            $category->increment('sold_count', $validated['quantity']);

            return redirect()->route('checkout.success', $transaction->reference_no);
        });
    }

    public function success($reference)
    {
        $transaction = Transaction::where('reference_no', $reference)->with('tickets.category', 'event')->firstOrFail();
        return view('checkout.success', compact('transaction'));
    }
}
