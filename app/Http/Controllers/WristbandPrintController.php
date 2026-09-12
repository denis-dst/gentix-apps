<?php

namespace App\Http\Controllers;

use App\Models\TicketCategory;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WristbandPrintController extends Controller
{
    /**
     * Display a printable view of wristbands for a specific category.
     */
    public function print(Request $request, TicketCategory $category)
    {
        // Check authorization
        $this->authorizeAccess($category->event);

        $status = $request->get('status', 'all'); 
        
        $query = Ticket::where('ticket_category_id', $category->id)
            ->with([
                'transaction',
                'category' => function ($q) {
                    $q->select('id', 'name', 'hex_color');
                },
                'event'
            ]);

        if ($status !== 'all') {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', ['sold', 'redeemed']);
        }

        $tickets = $query->orderBy('ticket_code', 'asc')->get();

        // If no tickets exist in DB yet, handle generation if requested
        if ($tickets->isEmpty()) {
            if ($request->has('generate_offline')) {
                $count = (int) $request->get('count', 10);
                $limit = max(1, min($count, 2000));

                \Illuminate\Support\Facades\DB::transaction(function() use ($category, $limit) {
                    $transaction = Transaction::create([
                        'tenant_id' => $category->tenant_id,
                        'event_id' => $category->event_id,
                        'ticket_category_id' => $category->id,
                        'quantity' => $limit,
                        'reference_no' => 'STOCK-' . strtoupper(Str::random(10)),
                        'customer_name' => 'OFFLINE STOCK',
                        'customer_email' => 'offline@gentix.id',
                        'customer_phone' => '-',
                        'customer_nik' => '0000000000000000',
                        'total_amount' => $category->price * $limit,
                        'payment_status' => 'paid',
                        'channel' => 'pos',
                        'payment_method' => 'OFFLINE STOCK',
                        'paid_at' => now(),
                    ]);

                    for ($i = 0; $i < $limit; $i++) {
                        Ticket::create([
                            'tenant_id' => $category->tenant_id,
                            'event_id' => $category->event_id,
                            'transaction_id' => $transaction->id,
                            'ticket_category_id' => $category->id,
                            'ticket_code' => 'GTX-OFF-' . strtoupper(Str::random(10)),
                            'status' => 'sold',
                        ]);
                    }
                });

                return redirect()->route('organizer.categories.print-wristbands', $category);
            }

            return back()->with('error', 'Belum ada tiket untuk dicetak pada kategori ini.');
        }

        return view('wristbands.print', [
            'tickets' => $tickets,
            'category' => $category,
            'event' => $category->event
        ]);
    }

    /**
     * Reset/clear dummy offline stock from category.
     */
    public function resetOfflineStock(TicketCategory $category)
    {
        $this->authorizeAccess($category->event);

        $offlineTransactions = Transaction::where('ticket_category_id', $category->id)
            ->where(function($q) {
                $q->where('customer_name', 'OFFLINE STOCK')
                  ->orWhere('payment_method', 'OFFLINE STOCK')
                  ->orWhere('reference_no', 'like', 'STOCK-%');
            })
            ->get();

        $deletedTicketsCount = 0;
        foreach ($offlineTransactions as $tx) {
            $deletedTicketsCount += $tx->tickets()->delete();
            $tx->delete();
        }

        // Recalculate real sold count from legitimate transactions
        $realSoldCount = Ticket::where('ticket_category_id', $category->id)
            ->whereIn('status', ['sold', 'redeemed'])
            ->whereHas('transaction', function($q) {
                $q->where('customer_name', '!=', 'OFFLINE STOCK')
                  ->where('payment_status', 'paid');
            })
            ->count();

        $category->update(['sold_count' => $realSoldCount]);

        return back()->with('success', "Berhasil mereset {$deletedTicketsCount} tiket stok dummy offline. Kuota penjualan online untuk kategori {$category->name} telah dipulihkan!");
    }

    private function authorizeAccess(Event $event)
    {
        if (auth()->user()->hasRole('Superadmin')) {
            return;
        }

        if ($event->tenant_id !== auth()->user()->tenant_id) {
            abort(403, 'Unauthorized access to this event');
        }
    }
}

