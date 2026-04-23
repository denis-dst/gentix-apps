<?php

namespace App\Services;

use App\Models\Ticket;
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
            $email = $ticket->visitor_data['email'] ?? $ticket->transaction->customer_email ?? null;
            
            if ($email) {
                Mail::to($email)->send(new EVoucherMail($ticket));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send e-voucher email for ticket ' . $ticket->ticket_code . ': ' . $e->getMessage());
        }
    }

    protected function sendWhatsApp(Ticket $ticket)
    {
        try {
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
            // $this->sendViaFonnte($phone, $message);
            
            Log::info("WA Notification (Mock) sent to {$phone}: {$message}");

        } catch (\Exception $e) {
            Log::error('Failed to send e-voucher WA for ticket ' . $ticket->ticket_code . ': ' . $e->getMessage());
        }
    }

    protected function sendViaFonnte($phone, $message)
    {
        $token = config('services.fonnte.token');
        
        // curl call to fonnte
        // ...
    }
}
