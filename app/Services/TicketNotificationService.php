<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Setting;
use App\Mail\EVoucherMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketNotificationService
{
    /**
     * Send e-voucher to email and WhatsApp.
     */
    public function sendEVoucher(Ticket $ticket)
    {
        // STRICT SAFETY CHECK:
        // E-voucher must NEVER be sent if payment is not paid (unless free event)
        if ($ticket->transaction && $ticket->transaction->payment_method !== 'free' && $ticket->transaction->payment_status !== 'paid') {
            Log::warning("sendEVoucher blocked: Transaction {$ticket->transaction->reference_no} is not paid (status: {$ticket->transaction->payment_status})");
            return;
        }

        // 1. Send Email
        $this->sendEmail($ticket);

        // 2. Send WhatsApp
        $this->sendWhatsApp($ticket);
    }

    protected function sendEmail(Ticket $ticket)
    {
        try {
            // STRICT SAFETY CHECK
            if ($ticket->transaction && $ticket->transaction->payment_method !== 'free' && $ticket->transaction->payment_status !== 'paid') {
                Log::warning("sendEmail blocked: Transaction {$ticket->transaction->reference_no} is not paid");
                return;
            }

            // Check global setting first
            $globalEmailEnabled = Setting::where('key', 'global_email_notifications_enabled')->value('value');
            if ($globalEmailEnabled === '0' || $globalEmailEnabled === false) {
                return;
            }

            $email = $ticket->visitor_data['email'] ?? $ticket->transaction->customer_email ?? null;

            if ($email) {
                Mail::to($email)->send(new EVoucherMail($ticket->transaction));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send e-voucher email for ticket ' . $ticket->ticket_code . ': ' . $e->getMessage());
        }
    }

    public function sendWhatsApp(Ticket $ticket, bool $force = false, bool $throwExceptions = false)
    {
        // STRICT SAFETY CHECK:
        // WhatsApp E-Voucher must ONLY be sent when transaction payment is confirmed paid (or free/promo event)
        if ($ticket->transaction && !in_array($ticket->transaction->payment_method, ['free', 'promo']) && $ticket->transaction->payment_status !== 'paid') {
            Log::warning("sendWhatsApp blocked: Transaction {$ticket->transaction->reference_no} is not paid (status: {$ticket->transaction->payment_status})");
            if ($throwExceptions) {
                throw new \Exception('Pembayaran transaksi belum berstatus berhasil/lunas.');
            }
            return;
        }

        if (!$force) {
            // Check global setting first
            $globalWaEnabled = Setting::where('key', 'global_wa_notifications_enabled')->value('value');
            if ($globalWaEnabled === '0' || $globalWaEnabled === false) {
                return;
            }
        }

        $phone = $ticket->visitor_data['phone'] ?? $ticket->transaction->customer_phone ?? null;

        if (!$phone) {
            if ($throwExceptions) {
                throw new \Exception('Nomor WhatsApp pelanggan tidak ditemukan.');
            }
            return;
        }

        $eventName = $ticket->event->name;
        $ticketCode = $ticket->ticket_code;
        $categoryName = $ticket->category->name;
        $url = config('app.url') . "/tickets/view/{$ticketCode}";
        $customerName = $ticket->transaction->customer_name ?? 'Pelanggan';
        $quantity = $ticket->transaction->quantity ?? 1;

        if ($quantity > 1) {
            $message = "*E-Voucher {$eventName}*\n\n";
            $message .= "Halo {$customerName}, terima kasih atas pemesanan tiket Anda.\n\n";
            $message .= "Detail Pemesanan:\n";
            $message .= "No. Invoice: {$ticket->transaction->reference_no}\n";
            $message .= "Kategori: {$categoryName}\n";
            $message .= "Jumlah: {$quantity} Tiket\n\n";
            $message .= "Silakan buka link E-Voucher berikut untuk melihat semua QR Code rombongan Anda:\n";
            $message .= "{$url}\n\n";
            $message .= "Sampai jumpa di lokasi acara!";
        } else {
            $message = "*E-Voucher {$eventName}*\n\n";
            $message .= "Halo {$customerName}, terima kasih telah melakukan pemesanan tiket.\n\n";
            $message .= "Detail Tiket:\n";
            $message .= "Kategori: {$categoryName}\n";
            $message .= "Kode Tiket: {$ticketCode}\n\n";
            $message .= "Silakan tunjukkan QR Code pada link berikut saat Check-in di lokasi:\n";
            $message .= "{$url}\n\n";
            $message .= "Sampai jumpa di lokasi acara!";
        }

        try {
            $this->sendViaFonnte($phone, $message, $throwExceptions);
            Log::info("WA Notification sent to {$phone}: {$message}");
        } catch (\Exception $e) {
            Log::error('Failed to send e-voucher WA for ticket ' . $ticket->ticket_code . ': ' . $e->getMessage());
            if ($throwExceptions) {
                throw $e;
            }
        }
    }

    protected function sendViaFonnte($phone, $message, bool $throwExceptions = false)
    {
        $token = config('services.fonnte.token');
        $sender = config('services.fonnte.sender');

        if (!$token) {
            Log::warning('Fonnte API token is not configured.');
            if ($throwExceptions) {
                throw new \Exception('Token Fonnte belum dikonfigurasi di server.');
            }
            return;
        }

        // Remove non-numeric characters first
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Format phone number to international format if starting with 0
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $payload = [
            'target' => $phone,
            'message' => $message,
            'countryCode' => '62',
        ];

        if ($sender) {
            $payload['sender'] = $sender;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === false) {
                    $reason = $data['reason'] ?? 'Unknown error';
                    Log::error("Fonnte WA API returned error status for {$phone}: {$reason}");
                    if ($throwExceptions) {
                        throw new \Exception("Fonnte API: " . $reason);
                    }
                } else {
                    Log::info("WA Notification sent successfully via Fonnte to {$phone}");
                }
            } else {
                Log::error("Fonnte WA API failed to send to {$phone}. Status: " . $response->status() . ", Response: " . $response->body());
                if ($throwExceptions) {
                    throw new \Exception("Koneksi Fonnte gagal (Status: " . $response->status() . ")");
                }
            }
        } catch (\Exception $e) {
            Log::error("Fonnte WA API exception while sending to {$phone}: " . $e->getMessage());
            if ($throwExceptions) {
                throw $e;
            }
        }
    }
}
