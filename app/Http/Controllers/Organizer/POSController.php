<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\Transaction;
use App\Services\TicketNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class POSController extends Controller
{
    public function index()
    {
        $events = Event::where('tenant_id', auth()->user()->tenant_id)
            ->whereIn('status', ['draft', 'published'])
            ->withCount('ticketCategories')
            ->orderByDesc('event_start_date')
            ->get();

        return view('organizer.pos.index', compact('events'));
    }

    public function create(Event $event)
    {
        $this->authorizeTenant($event);

        $event->load(['ticketCategories' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order')->orderBy('price');
        }]);

        return view('organizer.pos.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorizeTenant($event);

        $validated = $request->validate([
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'quantity' => 'required|integer|min:1|max:100',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:30',
            'customer_email' => 'required|email|max:255',
            'customer_nik' => 'nullable|string|max:32',
            'payment_method' => 'required|string|max:50',
        ]);

        $category = TicketCategory::where('event_id', $event->id)
            ->where('tenant_id', $event->tenant_id)
            ->where('is_active', true)
            ->findOrFail($validated['ticket_category_id']);

        if ($category->sold_count + $validated['quantity'] > $category->quota) {
            return back()->withInput()->with('error', 'Jumlah tiket melebihi sisa kuota kategori.');
        }

        $now = now();
        if ($category->sale_start_at && $now->lt($category->sale_start_at)) {
            return back()->withInput()->with('error', 'Tiket kategori ini belum tersedia.');
        }
        if ($category->sale_end_at && $now->gt($category->sale_end_at)) {
            return back()->withInput()->with('error', 'Penjualan tiket kategori ini sudah berakhir.');
        }

        $transaction = null;
        $tickets = collect();

        DB::transaction(function () use ($event, $category, $validated, &$transaction, &$tickets) {
            $transaction = Transaction::create([
                'tenant_id' => $event->tenant_id,
                'event_id' => $event->id,
                'ticket_category_id' => $category->id,
                'quantity' => $validated['quantity'],
                'reference_no' => 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_nik' => $validated['customer_nik'] ?? null,
                'discount_amount' => 0,
                'total_amount' => $category->price * $validated['quantity'],
                'payment_status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'channel' => 'pos',
                'processed_by' => auth()->id(),
                'paid_at' => now(),
            ]);

            for ($i = 0; $i < $validated['quantity']; $i++) {
                $tickets->push(Ticket::create([
                    'tenant_id' => $event->tenant_id,
                    'event_id' => $event->id,
                    'transaction_id' => $transaction->id,
                    'ticket_category_id' => $category->id,
                    'ticket_code' => 'GTX-' . strtoupper(Str::random(10)),
                    'status' => 'sold',
                    'visitor_data' => [
                        'name' => $validated['customer_name'],
                        'email' => $validated['customer_email'],
                        'phone' => $validated['customer_phone'],
                        'nik' => $validated['customer_nik'] ?? null,
                        'purchase_flow' => $event->purchase_flow ?? 'redeem',
                    ],
                ]));
            }

            $category->increment('sold_count', $validated['quantity']);
        });

        if (($event->purchase_flow ?? 'redeem') === 'evoucher') {
            foreach ($tickets as $ticket) {
                try {
                    app(TicketNotificationService::class)->sendEVoucher($ticket);
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        if (($event->purchase_flow ?? 'redeem') === 'print') {
            return redirect()->route('organizer.pos.print', $transaction);
        }

        $message = ($event->purchase_flow ?? 'redeem') === 'evoucher'
            ? 'Transaksi berhasil. E-Voucher telah dibuat dan QR dapat discan di gate.'
            : 'Transaksi berhasil. Lanjutkan proses redeem untuk pemasangan wristband.';

        return redirect()
            ->route('organizer.pos.create', $event)
            ->with('success', $message)
            ->with('active_evoucher_url', $tickets->first() ? route('tickets.view', $tickets->first()->ticket_code) : null);
    }

    public function print(Transaction $transaction)
    {
        $transaction->load(['event', 'tenant', 'tickets.category']);
        $this->authorizeTenant($transaction->event);

        if (($transaction->event->purchase_flow ?? 'redeem') !== 'print') {
            abort(403, 'Cetak termal hanya tersedia untuk event dengan flow Cetak Ticket Langsung.');
        }

        return view('organizer.pos.print', compact('transaction'));
    }

    private function authorizeTenant(Event $event): void
    {
        if ($event->tenant_id !== auth()->user()->tenant_id && !auth()->user()->hasRole('Superadmin')) {
            abort(403, 'Unauthorized access to this event');
        }
    }
}
