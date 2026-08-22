<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\Transaction;
use App\Services\TicketNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public function handleIPaymuNotification(Request $request)
    {
        \Log::info('iPaymu notification received', $request->all());

        // Support both JSON body and form-urlencoded payload from iPaymu callback
        $referenceId = $request->input('reference_id') ?: $request->input('referenceId');
        $statusCode  = (string) ($request->input('status_code') ?? $request->input('transaction_status_code', ''));
        $statusMsg   = strtolower((string) $request->input('status', ''));
        $statusDesc  = strtolower((string) $request->input('status_desc', ''));
        $paymentVia  = (string) $request->input('via', 'iPaymu');
        $channel     = (string) $request->input('channel', '');
        $paidAt      = $request->input('paid_at');

        if (empty($referenceId)) {
            \Log::warning('iPaymu notification received without reference_id', $request->all());
            return response()->json(['status' => 400, 'message' => 'Missing reference_id'], 400);
        }

        $dbTransaction = Transaction::where('reference_no', $referenceId)->first();

        if (!$dbTransaction) {
            \Log::warning("Transaction not found for iPaymu reference_id: {$referenceId}");
            return response()->json(['status' => 404, 'message' => 'Transaction not found'], 404);
        }

        // Format payment method string (e.g., "iPaymu (VA - BCA)")
        $methodParts = [];
        if (!empty($paymentVia)) {
            $methodParts[] = strtoupper($paymentVia);
        }
        if (!empty($channel)) {
            $methodParts[] = strtoupper($channel);
        }
        $paymentMethod = !empty($methodParts) ? 'iPaymu (' . implode(' - ', $methodParts) . ')' : 'iPaymu';

        // Parse status using standard iPaymu Status Codes
        $parsedStatus = \App\Services\IPaymuService::parseStatusCode($statusCode, '', $statusMsg ?: $statusDesc);

        if ($parsedStatus['is_paid']) {
            $updateData = [
                'payment_status' => 'paid',
                'payment_method' => $paymentMethod,
            ];
            if (!empty($paidAt)) {
                try {
                    $updateData['paid_at'] = \Carbon\Carbon::parse($paidAt);
                } catch (\Exception $e) {
                    $updateData['paid_at'] = now();
                }
            } else {
                $updateData['paid_at'] = now();
            }

            $dbTransaction->update($updateData);
            
            // Finalize transaction: generate tickets & send E-Voucher ONLY on confirmed payment
            $this->finalizeTransaction($dbTransaction);

            \Log::info("iPaymu transaction {$referenceId} marked as PAID. Tickets generated & Evoucher sent.");
        } elseif ($parsedStatus['is_failed']) {
            $statusToSet = $parsedStatus['internal_status'];
            $dbTransaction->update([
                'payment_status' => $statusToSet,
                'payment_method' => $paymentMethod,
            ]);

            \Log::info("iPaymu transaction {$referenceId} marked as {$statusToSet}. No tickets/voucher issued.");
        } else {
            $dbTransaction->update([
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
            ]);

            \Log::info("iPaymu transaction {$referenceId} is PENDING. No tickets/voucher issued.");
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Notification processed successfully',
            'data'    => [
                'reference_id'   => $referenceId,
                'payment_status' => $dbTransaction->payment_status,
            ]
        ], 200);
    }

    private function finalizeTransaction($transaction)
    {
        // STRICT SAFETY CHECK:
        // Never finalize or issue tickets/evoucher for unpaid transactions (unless free event or promo)
        if (!in_array($transaction->payment_method, ['free', 'promo']) && $transaction->payment_status !== 'paid') {
            \Log::warning("finalizeTransaction blocked: Transaction {$transaction->reference_no} is not paid (status: {$transaction->payment_status})");
            return;
        }

        // Don't duplicate tickets if already generated
        if ($transaction->tickets()->count() >= $transaction->quantity) {
            return;
        }

        // Collect created tickets OUTSIDE the closure so we can notify after commit
        $createdTickets = [];

        DB::transaction(function() use ($transaction, &$createdTickets) {
            $transaction->update([
                'payment_status' => 'paid',
                'paid_at' => $transaction->paid_at ?? now()
            ]);

            $category = $transaction->category;

            for ($i = 0; $i < $transaction->quantity; $i++) {
                $ticket = \App\Models\Ticket::create([
                    'tenant_id' => $transaction->tenant_id,
                    'event_id' => $transaction->event_id,
                    'transaction_id' => $transaction->id,
                    'ticket_category_id' => $transaction->ticket_category_id,
                    'ticket_code' => 'GTX-' . strtoupper(\Illuminate\Support\Str::random(10)),
                    'status' => 'sold',
                ]);

                $createdTickets[] = $ticket;
            }

            if ($category) {
                $category->increment('sold_count', $transaction->quantity);
            }
        });

        // Send notifications AFTER the DB transaction is committed
        // so SMTP errors/timeouts cannot cause DB rollback or browser fetch failures
        if (!empty($createdTickets)) {
            try {
                $notificationService = new \App\Services\TicketNotificationService();
                // Send primary E-Voucher email + WA for the first ticket
                $notificationService->sendEVoucher($createdTickets[0]);

                // For any remaining tickets in the same transaction, send WA notifications
                for ($i = 1; $i < count($createdTickets); $i++) {
                    $notificationService->sendWhatsApp($createdTickets[$i]);
                }
            } catch (\Exception $e) {
                \Log::error('Notification failed after finalizeTransaction for transaction ' . $transaction->reference_no . ': ' . $e->getMessage());
            }
        }
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->with(['ticketCategories' => function($query) {
                $query->where('is_active', true)->orderBy('price', 'asc');
            }, 'tenant'])
            ->firstOrFail();

        return view('events.show', compact('event'));
    }

    public function checkout(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Free event validation rules include gender and custom questions
        if ($event->is_free) {
            return $this->processFreeCheckout($request, $event);
        }

        // Paid event checkout flow
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

        // Normalize phone number to Fonnte format (starts with 62)
        if (isset($validated['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (str_starts_with($phone, '8')) {
                $phone = '62' . $phone;
            }
            $validated['phone'] = $phone;
        }

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

            // If 100% Promo Discount or 0 Rupiah, issue E-Voucher directly without iPaymu Payment Gateway
            if ($totalAmount <= 0) {
                $transaction = Transaction::create([
                    'tenant_id'          => $event->tenant_id,
                    'event_id'           => $event->id,
                    'ticket_category_id' => $category->id,
                    'quantity'           => $validated['quantity'],
                    'promo_code_id'      => $promo?->id,
                    'reference_no'       => $referenceNo,
                    'customer_name'      => $validated['name'],
                    'customer_email'     => $validated['email'],
                    'customer_phone'     => $validated['phone'],
                    'customer_nik'       => $validated['nik'],
                    'discount_amount'    => $discount,
                    'total_amount'       => 0,
                    'payment_status'     => 'paid',
                    'payment_method'     => 'promo',
                    'paid_at'            => now(),
                    'channel'            => 'online',
                ]);

                if ($promo) {
                    $promo->increment('used_count');
                }

                $this->finalizeTransaction($transaction);

                $redirectUrl = route('checkout.success', $referenceNo);

                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success'      => true,
                        'redirect_url' => $redirectUrl,
                        'reference_no' => $referenceNo,
                        'is_free'      => true
                    ]);
                }

                return redirect($redirectUrl);
            }

            // Create Transaction Record (Status: Pending for paid transactions)
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
                'payment_status'       => 'pending', // Initial status
                'payment_method'       => 'iPaymu',
                'channel'              => 'online',
            ]);

            if ($promo) {
                $promo->increment('used_count');
            }

            // iPaymu payment gateway integration
            $ipaymuService = new \App\Services\IPaymuService();
            $ipaymuResult  = $ipaymuService->createPaymentLink([
                'amount'         => $totalAmount,
                'invoice_number' => $referenceNo,
                'callback_url'   => route('checkout.success', $referenceNo),
                'notify_url'     => route('ipaymu.notification'),
                'failed_url'     => route('events.show', $event->slug),
                'line_items'     => [
                    [
                        'id'          => (string) $category->id,
                        'price'       => (int)($totalAmount / $validated['quantity']),
                        'quantity'    => $validated['quantity'],
                        'name'        => $category->name . ' - ' . $event->name,
                        'description' => 'Tiket ' . $category->name . ' - ' . $event->name,
                    ]
                ]
            ], [
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);

            if ($ipaymuResult['success']) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success'      => true,
                        'redirect_url' => $ipaymuResult['payment_url'],
                        'reference_no' => $referenceNo
                    ]);
                }
                return redirect($ipaymuResult['payment_url']);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $ipaymuResult['message'] ?? 'Gagal memproses pembayaran iPaymu.'
                ], 400);
            }
        });
    }

    /**
     * Handle Free Event Registration (no payment required).
     * Creates transaction + tickets immediately, then sends E-Voucher.
     */
    private function processFreeCheckout(Request $request, Event $event)
    {
        if (is_string($request->input('attendees'))) {
            $attendees = json_decode($request->input('attendees'), true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['attendees' => $attendees]);
            }
        }

        $proofs = $event->getRegistrationProofs();

        $rules = [
            'ticket_category_id' => 'required|exists:ticket_categories,id',
            'quantity'           => 'required|integer|min:1|max:' . ($event->max_tickets_per_transaction ?? 1),
            'phone'              => 'required|string|max:20',
            'email'              => 'required|email|max:255',
            'attendees'          => 'required|array|size:' . $request->input('quantity', 1),
            'attendees.*.name'   => 'required|string|max:255',
            'attendees.*.gender' => 'required|in:ikhwan,akhwat',
            'attendees.*.umroh_answer' => 'nullable|string|max:500',
        ];

        $messages = [];
        foreach ($proofs as $proof) {
            $key = 'proofs.' . $proof['id'];
            $rules[$key] = ($proof['is_required'] ? 'required|' : 'nullable|') . 'file|max:1024|mimes:jpeg,jpg,png';
            $messages[$key . '.required'] = "Upload " . $proof['label'] . " wajib diisi.";
            $messages[$key . '.max'] = "Ukuran " . $proof['label'] . " maksimal 1 MB.";
            $messages[$key . '.mimes'] = "Format " . $proof['label'] . " harus JPG atau PNG.";
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $validator->errors()->first()
            ], 422);
        }

        $validated = $validator->validated();

        // Normalize phone number to Fonnte format (starts with 62)
        if (isset($validated['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (str_starts_with($phone, '8')) {
                $phone = '62' . $phone;
            }
            $validated['phone'] = $phone;
        }

        $category = TicketCategory::findOrFail($validated['ticket_category_id']);

        // Quota Check
        if ($category->sold_count + $validated['quantity'] > $category->quota) {
            return response()->json(['success' => false, 'message' => 'Mohon maaf, kuota pendaftaran sudah penuh.']);
        }

        // Time Check
        $now = now();
        if ($category->sale_start_at && $now->lt($category->sale_start_at)) {
            return response()->json(['success' => false, 'message' => 'Pendaftaran untuk kategori ini belum dibuka.']);
        }
        if ($category->sale_end_at && $now->gt($category->sale_end_at)) {
            return response()->json(['success' => false, 'message' => 'Pendaftaran untuk kategori ini sudah ditutup.']);
        }

        $uploadedProofs = [];
        foreach ($proofs as $proof) {
            $key = $proof['id'];
            $fileKey = "proofs.{$key}";
            $hasFile = $request->hasFile($fileKey);
            if (!$hasFile && str_contains($fileKey, '.')) {
                $parts = explode('.', $fileKey);
                $files = $request->file($parts[0]);
                $hasFile = isset($files[$parts[1]]);
            }

            if ($hasFile) {
                $path = $this->storeRegistrationProof($request, $fileKey, $proof['label']);
                if ($path instanceof \Illuminate\Http\JsonResponse) {
                    return $path;
                }
                $uploadedProofs[$key] = $path;
            }
        }

        $proofIgPath = $uploadedProofs['proof_ig'] ?? null;
        $proofReviewPath = $uploadedProofs['proof_review'] ?? null;

        $firstTicket = null;
        $referenceNo  = null;

        DB::transaction(function () use ($event, $category, $validated, $proofIgPath, $proofReviewPath, $uploadedProofs, &$firstTicket, &$referenceNo) {
            $referenceNo = 'FREE-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $firstAttendee = $validated['attendees'][0];

            // Create Transaction: immediately paid (free event)
            $transaction = Transaction::create([
                'tenant_id'            => $event->tenant_id,
                'event_id'             => $event->id,
                'ticket_category_id'   => $category->id,
                'quantity'             => $validated['quantity'],
                'reference_no'         => $referenceNo,
                'customer_name'        => $firstAttendee['name'],
                'customer_email'       => $validated['email'],
                'customer_phone'       => $validated['phone'],
                'customer_nik'         => null,
                'customer_gender'      => $firstAttendee['gender'],
                'customer_umroh_answer'=> $firstAttendee['umroh_answer'] ?? null,
                'discount_amount'      => 0,
                'total_amount'         => 0,
                'payment_status'       => 'paid',
                'payment_method'       => 'free',
                'paid_at'              => now(),
                'channel'              => 'online',
            ]);

            // Create Tickets immediately
            for ($i = 0; $i < $validated['quantity']; $i++) {
                $attendee = $validated['attendees'][$i];
                $ticket = Ticket::create([
                    'tenant_id'          => $event->tenant_id,
                    'event_id'           => $event->id,
                    'transaction_id'     => $transaction->id,
                    'ticket_category_id' => $category->id,
                    'ticket_code'        => 'GTX-' . strtoupper(Str::random(10)),
                    'status'             => 'sold',
                    'visitor_data'       => [
                        'name'         => $attendee['name'],
                        'gender'       => $attendee['gender'],
                        'umroh_answer' => $attendee['umroh_answer'] ?? null,
                        'email'        => $validated['email'],
                        'phone'        => $validated['phone'],
                        'proof_ig'     => $proofIgPath,
                        'proof_review' => $proofReviewPath,
                        'proofs'       => $uploadedProofs,
                    ]
                ]);

                if ($i === 0) {
                    $firstTicket = $ticket;
                }
            }

            // Increment sold count
            $category->increment('sold_count', $validated['quantity']);
        });

        // Send notification AFTER the DB transaction is committed.
        // This prevents SMTP timeouts/errors from causing a DB rollback
        // or making the browser fetch() fail with "Failed to fetch".
        if ($firstTicket) {
            try {
                $notificationService = new TicketNotificationService();
                $notificationService->sendEVoucher($firstTicket);
            } catch (\Exception $e) {
                \Log::error('Notification failed after free checkout for ticket ' . $firstTicket->ticket_code . ': ' . $e->getMessage());
            }
        }

        return response()->json([
            'success'      => true,
            'reference_no' => $referenceNo,
            'ticket_code'  => $firstTicket ? $firstTicket->ticket_code : null,
        ]);
    }

    private function storeRegistrationProof(Request $request, string $key, string $label): string|\Illuminate\Http\JsonResponse
    {
        $file = $request->file($key);

        if (!$file && str_contains($key, '.')) {
            $parts = explode('.', $key);
            $files = $request->file($parts[0]);
            $file = $files[$parts[1]] ?? null;
        }

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => "Upload {$label} wajib diisi.",
            ], 422);
        }

        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => "Upload {$label} gagal diterima server: " . $file->getErrorMessage(),
            ], 422);
        }

        if (($file->getSize() ?: 0) > 1024 * 1024) {
            return response()->json([
                'success' => false,
                'message' => "Ukuran {$label} maksimal 1 MB.",
            ], 422);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = strtolower((string) $file->getMimeType());
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $allowedMimeTypes = ['image/jpeg', 'image/png'];

        if (!in_array($extension, $allowedExtensions, true) || !in_array($mimeType, $allowedMimeTypes, true)) {
            return response()->json([
                'success' => false,
                'message' => "Format {$label} harus JPG atau PNG.",
            ], 422);
        }

        $sourcePath = $file->getPathname();
        if (!$sourcePath) {
            return response()->json([
                'success' => false,
                'message' => "Upload {$label} gagal dibaca. Silakan pilih ulang file.",
            ], 422);
        }

        $fileName = Str::uuid() . '.' . ($extension === 'jpeg' ? 'jpg' : $extension);
        $path = 'registration-proofs/' . $fileName;

        try {
            $stream = fopen($sourcePath, 'r');

            if ($stream === false) {
                throw new \RuntimeException('Unable to open uploaded file stream.');
            }

            $stored = Storage::disk('public')->put($path, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        } catch (\Throwable $e) {
            if (isset($stream) && is_resource($stream)) {
                fclose($stream);
            }

            \Log::error("Failed storing {$label}: " . $e->getMessage(), [
                'field' => $key,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $mimeType,
                'source_path_empty' => $sourcePath === '',
            ]);

            return response()->json([
                'success' => false,
                'message' => "Upload {$label} gagal disimpan. Silakan coba lagi.",
            ], 500);
        }

        if (!$stored) {
            return response()->json([
                'success' => false,
                'message' => "Upload {$label} gagal disimpan. Silakan coba lagi.",
            ], 500);
        }

        return $path;
    }

    public function success($reference)
    {
        $transaction = Transaction::where('reference_no', $reference)->with('tickets.category', 'event')->firstOrFail();

        // Free events or 100% Promo discounts are already paid and finalized during checkout
        if ($transaction->payment_method === 'free' || $transaction->payment_method === 'promo' || $transaction->total_amount == 0) {
            return view('checkout.success', compact('transaction'));
        }

        // If tickets are already generated and transaction is paid
        if ($transaction->payment_status === 'paid' && $transaction->tickets->isNotEmpty()) {
            return view('checkout.success', compact('transaction'));
        }

        // If tickets are not generated yet, check status
        if ($transaction->tickets->isEmpty()) {
            $statusParam     = strtolower((string) request()->query('status', ''));
            $statusCodeParam = (string) (request()->query('status_code', request()->query('transaction_status_code', '')));
            $trxIdParam      = (string) request()->query('trx_id', '');

            // Parse status from URL query parameters if present
            $hasQueryStatus = !empty($statusCodeParam) || !empty($statusParam);
            $parsedQuery = $hasQueryStatus ? \App\Services\IPaymuService::parseStatusCode($statusCodeParam, '', $statusParam) : null;

            if ($parsedQuery && $parsedQuery['is_failed']) {
                $transaction->update(['payment_status' => $parsedQuery['internal_status']]);
                $transaction->refresh();
            } elseif ($transaction->payment_status === 'paid' || ($parsedQuery && $parsedQuery['is_paid'])) {
                $transaction->update(['payment_status' => 'paid']);
                $this->finalizeTransaction($transaction);
                $transaction->refresh();
                $transaction->load('tickets.category', 'event');
            } elseif (!empty($trxIdParam) && !empty(config('services.ipaymu.va')) && !empty(config('services.ipaymu.api_key'))) {
                // If we have trx_id from return URL, verify status directly with iPaymu /transaction API
                $ipaymuService = new \App\Services\IPaymuService();
                $checkResult = $ipaymuService->checkTransactionStatus($trxIdParam);
                if ($checkResult['success'] && $checkResult['is_paid']) {
                    $transaction->update(['payment_status' => 'paid']);
                    $this->finalizeTransaction($transaction);
                    $transaction->refresh();
                    $transaction->load('tickets.category', 'event');
                } elseif ($checkResult['success'] && $checkResult['is_failed']) {
                    $transaction->update(['payment_status' => $checkResult['internal_status']]);
                    $transaction->refresh();
                }
            }
        }

        return view('checkout.success', compact('transaction'));
    }

    public function evoucher($reference)
    {
        $transaction = Transaction::where('reference_no', $reference)
            ->with(['event', 'tickets.category', 'tenant'])
            ->firstOrFail();
            
        return view('organizer.transactions.evoucher', compact('transaction'));
    }
}
