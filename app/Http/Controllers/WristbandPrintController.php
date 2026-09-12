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
     * Display a printable view of wristbands for a specific category based on quota/stock.
     */
    public function print(Request $request, TicketCategory $category)
    {
        // Check authorization
        $this->authorizeAccess($category->event);

        $category->load('event');
        $event = $category->event;

        // Total count based on category stock/quota
        $quota = (int) ($category->quota > 0 ? $category->quota : 50);
        $count = (int) $request->get('count', $quota);
        $count = max(1, min($count, 5000));
        $start = max(1, (int) $request->get('start', 1));

        $wristbands = collect();
        for ($i = $start; $i < $start + $count; $i++) {
            $code = sprintf('WB-C%d-%04d', $category->id, $i);
            $wristbands->push((object) [
                'ticket_code' => $code,
                'wristband_code' => $code,
                'category' => $category,
                'event' => $event,
                'index' => $i
            ]);
        }

        return view('wristbands.print', [
            'tickets' => $wristbands,
            'category' => $category,
            'event' => $event,
            'totalCount' => $count,
            'startNumber' => $start,
            'categoryQuota' => $quota
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

