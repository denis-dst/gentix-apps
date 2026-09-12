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

        $type = $request->get('type'); // 'sold', 'redeem', 'blank', or null
        $count = (int) $request->get('count', $category->quota ?: 50);
        $count = max(1, min($count, 5000)); // sane limit

        // If explicitly requested 'sold'
        if ($type === 'sold') {
            $tickets = Ticket::where('ticket_category_id', $category->id)
                ->where('status', 'sold')
                ->with([
                    'transaction',
                    'category' => function ($q) {
                        $q->select('id', 'name', 'hex_color');
                    },
                    'event'
                ])
                ->orderBy('ticket_code', 'asc')
                ->get();

            if ($tickets->isEmpty()) {
                return back()->with('error', 'Belum ada tiket terjual untuk kategori ini.');
            }

            return view('wristbands.print', [
                'tickets' => $tickets,
                'category' => $category,
                'event' => $category->event
            ]);
        }

        // Default / Redeem Wristbands: Generate virtual wristbands on-the-fly
        // This does NOT affect online sale quota and does NOT create fake transactions.
        $tickets = collect();
        $catPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $category->name), 0, 4)) ?: 'CAT';

        for ($i = 1; $i <= $count; $i++) {
            $uniqueCode = sprintf('WB-%s-%s%04d', $catPrefix, strtoupper(Str::random(3)), $i);
            $ticket = new Ticket([
                'ticket_code' => $uniqueCode,
                'status' => 'sold',
            ]);
            $ticket->setRelation('category', $category);
            $ticket->setRelation('event', $category->event);
            $tickets->push($ticket);
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

