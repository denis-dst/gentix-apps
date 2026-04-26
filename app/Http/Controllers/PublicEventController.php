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

use App\Models\PromoCode;

class PublicEventController extends Controller
{
    public function validatePromo(Request $request)
    {
        $code = $request->code;
        $eventId = $request->event_id;
        $amount = $request->amount;

        $promo = PromoCode::where('code', $code)
            ->where('is_active', true)
            ->where(function($q) use ($eventId) {
                $q->whereNull('event_id')->orWhere('event_id', $eventId);
            })
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Kode promo tidak valid.']);
        }

        if ($promo->start_at && now()->lt($promo->start_at)) {
            return response()->json(['success' => false, 'message' => 'Promo belum dimulai.']);
        }

        if ($promo->expires_at && now()->gt($promo->expires_at)) {
            return response()->json(['success' => false, 'message' => 'Promo sudah berakhir.']);
        }

        if ($promo->max_usage && $promo->used_count >= $promo->max_usage) {
            return response()->json(['success' => false, 'message' => 'Kuota promo sudah habis.']);
        }

        $discount = 0;
        if ($promo->type === 'percentage') {
            $discount = ($promo->value / 100) * $amount;
        } else {
            $discount = $promo->value;
        }

        return response()->json([
            'success' => true,
            'promo_id' => $promo->id,
            'discount' => $discount,
            'message' => 'Promo berhasil digunakan!'
        ]);
    }

    public function handleNotification(Request $request)
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');

        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        $dbTransaction = Transaction::where('reference_no', $orderId)->first();

        if (!$dbTransaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $dbTransaction->update(['payment_status' => 'pending']);
                } else {
                    $dbTransaction->update(['payment_method' => $type]);
                    $this->finalizeTransaction($dbTransaction);
                }
            }
        } else if ($transaction == 'settlement') {
            $dbTransaction->update(['payment_method' => $type]);
            $this->finalizeTransaction($dbTransaction);
        } else if ($transaction == 'pending') {
            $dbTransaction->update(['payment_status' => 'pending', 'payment_method' => $type]);
        } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            $dbTransaction->update(['payment_status' => 'failed', 'payment_method' => $type]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function finalizeTransaction($transaction)
    {
        if ($transaction->payment_status === 'paid') return;

        DB::transaction(function() use ($transaction) {
            $transaction->update([
                'payment_status' => 'paid',
                'paid_at' => now()
            ]);

            $category = $transaction->category;
            $event = $transaction->event;

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
                $notificationService = new \App\Services\TicketNotificationService();
                $notificationService->sendEVoucher($ticket);
            }

            if ($category) {
                $category->increment('sold_count', $transaction->quantity);
            }
        });
    }

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
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'quantity' => 'required|integer|min:1|max:10', // Increased to 10 for testing
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'nik' => 'required|string|size:16',
            'promo_code_id' => 'nullable|exists:promo_codes,id',
            'discount_amount' => 'nullable|numeric|min:0',
            'notif_wa' => 'nullable',
            'notif_email' => 'nullable',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Validasi gagal: ' . $validator->errors()->first()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $category = TicketCategory::findOrFail($validated['ticket_category_id']);
        
        // Final Promo Validation (Backend)
        $promo = null;
        $discount = 0;
        if (!empty($validated['promo_code_id'])) {
            $promo = PromoCode::find($validated['promo_code_id']);
            if ($promo && $promo->is_active) {
                // Re-calculate discount to prevent tampering
                $subtotal = $category->price * $validated['quantity'];
                if ($promo->type === 'percentage') {
                    $discount = ($promo->value / 100) * $subtotal;
                } else {
                    $discount = $promo->value;
                }
                // Limit discount to subtotal
                $discount = min($discount, $subtotal);
            }
        }

        // NIK Prefix Restriction Check
        if ($category->nik_restriction) {
            $prefixes = array_map('trim', explode(',', $category->nik_restriction));
            $match = false;
            foreach ($prefixes as $prefix) {
                if (str_starts_with($validated['nik'], $prefix)) {
                    $match = true;
                    break;
                }
            }
            if (!$match) {
                $message = $category->nik_restriction_message ?: 'Mohon Maaf, NIK Anda Tidak Diizinkan Untuk Melakukan Transaksi Ini';
                if ($request->ajax()) return response()->json(['success' => false, 'message' => $message]);
                return back()->with('error', $message);
            }
        }

        // Availability Check
        if ($category->sold_count + $validated['quantity'] > $category->quota) {
            $message = 'Mohon maaf, jumlah tiket melebihi kuota tersedia.';
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $message]);
            return back()->with('error', $message);
        }

        // Time Check
        $now = now();
        if ($category->sale_start_at && $now->lt($category->sale_start_at)) {
            $message = 'Tiket untuk kategori ini belum tersedia.';
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $message]);
            return back()->with('error', $message);
        }
        if ($category->sale_end_at && $now->gt($category->sale_end_at)) {
            $message = 'Penjualan tiket untuk kategori ini sudah berakhir.';
            if ($request->ajax()) return response()->json(['success' => false, 'message' => $message]);
            return back()->with('error', $message);
        }

        return DB::transaction(function () use ($event, $category, $validated, $promo, $discount) {
            $subtotal = $category->price * $validated['quantity'];
            $totalAmount = max(0, $subtotal - $discount);
            $referenceNo = 'TX-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Create Transaction Record (Status: Pending)
            $transaction = Transaction::create([
                'tenant_id' => $event->tenant_id,
                'event_id' => $event->id,
                'ticket_category_id' => $category->id,
                'quantity' => $validated['quantity'],
                'promo_code_id' => $promo?->id,
                'reference_no' => $referenceNo,
                'customer_name' => $validated['name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'],
                'customer_nik' => $validated['nik'],
                'discount_amount' => $discount,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending', // Initial status
                'payment_method' => 'Midtrans Snap',
                'channel' => 'online',
            ]);

            if ($promo) {
                $promo->increment('used_count');
            }

            // --- Midtrans Integration ---
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $referenceNo,
                    'gross_amount' => (int)$totalAmount,
                ],
                'customer_details' => [
                    'first_name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ],
                'item_details' => [
                    [
                        'id' => $category->id,
                        'price' => (int)($totalAmount / $validated['quantity']),
                        'quantity' => $validated['quantity'],
                        'name' => $category->name . ' - ' . $event->name,
                    ]
                ]
            ];

            try {
                $snapToken = \Midtrans\Snap::getSnapToken($params);
                
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'snap_token' => $snapToken,
                        'reference_no' => $referenceNo
                    ]);
                }

                // If not AJAX, we might need a dedicated payment page or just redirect back with token
                return view('checkout.payment', compact('transaction', 'snapToken'));

            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        });
    }

    public function success($reference)
    {
        $transaction = Transaction::where('reference_no', $reference)->with('tickets.category', 'event')->firstOrFail();

        // If tickets are not generated yet, try to check status with Midtrans
        if ($transaction->tickets->isEmpty()) {
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production');

            try {
                $status = \Midtrans\Transaction::status($reference);
                
                if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                    // Update payment method from real status
                    $transaction->update(['payment_method' => $status->payment_type]);
                    $this->finalizeTransaction($transaction);
                    
                    // Refresh transaction data
                    $transaction->load('tickets.category');
                }
            } catch (\Exception $e) {
                // Silently fail if Midtrans check fails, user will see the processing state
                \Log::error('Midtrans status check failed: ' . $e->getMessage());
            }
        }

        return view('checkout.success', compact('transaction'));
    }
}
