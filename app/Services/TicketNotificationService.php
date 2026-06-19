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
        // 1. Send Email
        $this->sendEmail($ticket);

        // 2. Send WhatsApp
        $this->sendWhatsApp($ticket);
    }

    protected function sendEmail(Ticket $ticket)
    {
        try {
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

    protected function sendWhatsApp(Ticket $ticket)
    {
        try {
            // Check global setting first
            $globalWaEnabled = Setting::where('key', 'global_wa_notifications_enabled')->value('value');
            if ($globalWaEnabled === '0' || $globalWaEnabled === false) {
                return;
            }

            $phone = $ticket->visitor_data['phone'] ?? $ticket->transaction->customer_phone ?? null;
            
            if (!$phone) return;

            $eventName = $ticket->event->name;
            $ticketCode = $ticket->ticket_code;
            $categoryName = $ticket->category->name;
            $url = config('app.url') . "/tickets/view/{$ticketCode}";

            $message = "*E-Voucher {$eventName}*\n\n";
            $message .= "Halo, terima kasih telah melakukan pembelian tiket.\n\n";
            $message .= "Detail Tiket:\n";
            $message .= "Kategori: {$categoryName}\n";
            $message .= "Kode Tiket: {$ticketCode}\n\n";
            $message .= "Silakan tunjukkan QR Code pada link berikut saat penukaran gelang (redemption):\n";
            $message .= "{$url}\n\n";
            $message .= "Sampai jumpa di lokasi!";

            // Example of Fonnte integration (placeholder)
            $this->sendViaFonnte($phone, $message);
            
            Log::info("WA Notification sent to {$phone}: {$message}");

        } catch (\Exception $e) {
            Log::error('Failed to send e-voucher WA for ticket ' . $ticket->ticket_code . ': ' . $e->getMessage());
        }
    }

    protected function sendViaFonnte($phone, $message)
    {
        $token = config('services.fonnte.token');
        $sender = config('services.fonnte.sender');

        if (!$token) {
            Log::warning('Fonnte API token is not configured.');
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
                Log::info("WA Notification sent successfully via Fonnte to {$phone}");
            } else {
                Log::error("Fonnte WA API failed to send to {$phone}. Status: " . $response->status() . ", Response: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Fonnte WA API exception while sending to {$phone}: " . $e->getMessage());
        }
    }
}
