<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\TicketNotificationService;

class POSController extends Controller
{
    /**
     * Penjualan Langsung (On-the-spot)
     */
    public function sellTicket(Request $request, Event $event)
    {
        $this->authorizeTenant($event);

        $request->validate([
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'customer_name' => 'required',
            'customer_email' => 'required|email',
            'customer_phone' => 'required',
            'customer_nik' => 'required',
        ]);

        $category = TicketCategory::find($request->ticket_category_id);
        
        // Quota Check
        if ($category->sold_count >= $category->quota) {
            return response()->json(['message' => 'Quota full'], 422);
        }

        // Release Time Check
        $now = now();
        if ($category->sale_start_at && $now->lt($category->sale_start_at)) {
            return response()->json(['message' => 'Ticket is not yet available for sale until ' . $category->sale_start_at->format('d M Y H:i')], 422);
        }
        if ($category->sale_end_at && $now->gt($category->sale_end_at)) {
            return response()->json(['message' => 'Ticket sales have ended'], 422);
        }

        return DB::transaction(function () use ($request, $event, $category) {
            // Create Transaction
            $transaction = Transaction::create([
                'tenant_id' => $event->tenant_id,
                'event_id' => $event->id,
                'reference_no' => 'POS-' . strtoupper(Str::random(10)),
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_nik' => $request->customer_nik,
                'total_amount' => $category->price,
                'payment_status' => 'paid',
                'channel' => 'pos',
                'processed_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            // Create Ticket
            $ticket = Ticket::create([
                'tenant_id' => $event->tenant_id,
                'event_id' => $event->id,
                'transaction_id' => $transaction->id,
                'ticket_category_id' => $category->id,
                'ticket_code' => 'GTX-' . strtoupper(Str::random(12)),
                'status' => 'sold',
            ]);

            $category->increment('sold_count');

            // Send E-Voucher
            app(TicketNotificationService::class)->sendEVoucher($ticket);

            return response()->json(['message' => 'Ticket sold and E-Voucher sent', 'ticket' => $ticket]);
        });
    }

    /**
     * Validasi & Asimilasi E-Voucher (Redemption)
     */
    public function redeemTicket(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|exists:tickets,ticket_code',
            'wristband_qr' => 'required|unique:tickets,wristband_qr'
        ]);

        $ticket = Ticket::where('ticket_code', $request->ticket_code)->first();

        $this->authorizeTenant($ticket->event);

        if ($ticket->status === 'redeemed') {
            return response()->json(['message' => 'Ticket already redeemed at ' . $ticket->redeemed_at], 422);
        }

        $ticket->update([
            'wristband_qr' => $request->wristband_qr,
            'status' => 'redeemed',
            'redeemed_at' => now(),
            'redeemed_by' => auth()->id()
        ]);

        return response()->json([
            'message' => 'Redemption successful. Wristband linked.',
            'visitor' => $ticket->transaction->customer_name,
            'category' => $ticket->category->name
        ]);
    }

    private function authorizeTenant(Event $event)
    {
        // Skip authorization for Superadmin if needed, but for now strict to tenant_id
        if ($event->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized access to this event');
        }
    }
}
