<?php

namespace App\Http\Controllers;

use App\Models\TicketCategory;
use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Http\Request;

class WristbandPrintController extends Controller
{
    /**
     * Display a printable view of wristbands for a specific category.
     */
    public function print(Request $request, TicketCategory $category)
    {
        // Check authorization
        $this->authorizeAccess($category->event);

        $status = $request->get('status', 'sold'); 
        
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
        }

        $tickets = $query->orderBy('ticket_code', 'asc')->get();

        if ($tickets->isEmpty()) {
            // Jika user minta generate tiket stok (offline)
            if ($request->has('generate_offline')) {
                $count = (int) $request->get('count', 10); // Default 10 tiket
                $limit = min($count, $category->quota - $category->tickets()->count());
                
                if ($limit <= 0) {
                    return back()->with('error', 'Kuota tidak mencukupi untuk generate tiket tambahan.');
                }

                \Illuminate\Support\Facades\DB::transaction(function() use ($category, $limit) {
                    // Create a dummy transaction for offline stock
                    $transaction = \App\Models\Transaction::create([
                        'tenant_id' => $category->tenant_id,
                        'event_id' => $category->event_id,
                        'ticket_category_id' => $category->id,
                        'quantity' => $limit,
                        'reference_no' => 'STOCK-' . strtoupper(\Illuminate\Support\Str::random(10)),
                        'customer_name' => 'OFFLINE STOCK',
                        'customer_email' => 'offline@gentix.id',
                        'customer_phone' => '-',
                        'customer_nik' => '0000000000000000',
                        'total_amount' => $category->price * $limit,
                        'payment_status' => 'paid',
                        'channel' => 'offline_stock',
                        'paid_at' => now(),
                    ]);

                    for ($i = 0; $i < $limit; $i++) {
                        Ticket::create([
                            'tenant_id' => $category->tenant_id,
                            'event_id' => $category->event_id,
                            'transaction_id' => $transaction->id,
                            'ticket_category_id' => $category->id,
                            'ticket_code' => 'GTX-OFF-' . strtoupper(\Illuminate\Support\Str::random(10)),
                            'status' => 'sold', // Set to sold so it appears in print
                        ]);
                    }

                    $category->increment('sold_count', $limit);
                });
                
                return redirect()->route('organizer.categories.print-wristbands', $category);
            }

            return back()->with('error', 'Tidak ada tiket untuk dicetak. Jika ingin cetak tiket stok untuk penjualan offline, silakan generate tiket terlebih dahulu.');
        }

        return view('wristbands.print', [
            'tickets' => $tickets,
            'category' => $category,
            'event' => $category->event
        ]);
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
