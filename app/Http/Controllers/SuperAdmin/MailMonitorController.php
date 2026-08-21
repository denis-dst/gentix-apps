<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\MailboxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailMonitorController extends Controller
{
    protected MailboxService $mailboxService;

    public function __construct(MailboxService $mailboxService)
    {
        $this->mailboxService = $mailboxService;
    }

    public function index(Request $request)
    {
        // Outgoing SMTP Config
        $smtpConfig = [
            'host'       => config('mail.mailers.smtp.host', 'mail.gentix-apps.com'),
            'port'       => config('mail.mailers.smtp.port', 465),
            'scheme'     => config('mail.mailers.smtp.scheme', 'smtps'),
            'username'   => config('mail.mailers.smtp.username', 'no-reply@gentix-apps.com'),
            'from_email' => config('mail.from.address', 'no-reply@gentix-apps.com'),
            'from_name'  => config('mail.from.name', 'GenTix Apps'),
            'has_password' => !empty(config('mail.mailers.smtp.password')),
        ];

        // Incoming IMAP/POP3 Config
        $incomingConfig = [
            'host'      => 'mail.gentix-apps.com',
            'imap_port' => 993,
            'pop3_port' => 995,
            'username'  => config('mail.mailers.smtp.username', 'no-reply@gentix-apps.com'),
        ];

        // Recent Sent Emails (from Transactions E-Voucher)
        $sentTransactions = Transaction::with(['event', 'tickets.category'])
            ->whereNotNull('customer_email')
            ->where('payment_status', 'paid')
            ->orderByDesc('paid_at')
            ->orderByDesc('updated_at')
            ->take(30)
            ->get();

        return view('superadmin.mail.index', compact('smtpConfig', 'incomingConfig', 'sentTransactions'));
    }

    /**
     * AJAX: Test Incoming Connection (IMAP / POP3)
     */
    public function testIncoming(Request $request)
    {
        $protocol = $request->input('protocol', 'imap');
        $host     = $request->input('host', 'mail.gentix-apps.com');
        $port     = (int) $request->input('port', $protocol === 'pop3' ? 995 : 993);
        $username = $request->input('username', config('mail.mailers.smtp.username', 'no-reply@gentix-apps.com'));
        $password = $request->input('password') ?: config('mail.mailers.smtp.password', '');

        if (empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password email belum diisi di form atau di file .env (MAIL_PASSWORD).',
            ], 422);
        }

        $result = $this->mailboxService->testIncomingConnection($protocol, $host, $port, $username, $password);

        return response()->json($result);
    }

    /**
     * AJAX: Fetch Inbox Emails via IMAP
     */
    public function fetchInbox(Request $request)
    {
        $host     = $request->input('host', 'mail.gentix-apps.com');
        $port     = (int) $request->input('port', 993);
        $username = $request->input('username', config('mail.mailers.smtp.username', 'no-reply@gentix-apps.com'));
        $password = $request->input('password') ?: config('mail.mailers.smtp.password', '');
        $limit    = (int) $request->input('limit', 20);

        if (empty($password)) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan masukkan password email akun cPanel untuk membaca kotak masuk.',
                'messages' => [],
            ]);
        }

        $result = $this->mailboxService->getInboxMessages($host, $port, $username, $password, $limit);

        return response()->json($result);
    }

    /**
     * AJAX: Read specific email body
     */
    public function readMessage(Request $request, $id)
    {
        $host     = $request->input('host', 'mail.gentix-apps.com');
        $port     = (int) $request->input('port', 993);
        $username = $request->input('username', config('mail.mailers.smtp.username', 'no-reply@gentix-apps.com'));
        $password = $request->input('password') ?: config('mail.mailers.smtp.password', '');

        $result = $this->mailboxService->getMessageDetail($host, $port, $username, $password, (int) $id);

        return response()->json($result);
    }

    /**
     * AJAX: Send Test Email via Outgoing SMTP
     */
    public function testSmtp(Request $request)
    {
        $request->validate([
            'to_email' => 'required|email',
            'subject'  => 'nullable|string',
            'body'     => 'nullable|string',
        ]);

        $toEmail = $request->input('to_email');
        $subject = $request->input('subject') ?: 'Uji Coba Pengiriman Email GenTix Apps (' . date('d M Y H:i:s') . ')';
        $body    = $request->input('body') ?: "Halo,\n\nIni adalah email uji coba dari server GenTix Apps untuk memverifikasi bahwa konfigurasi SMTP cPanel (Port 465 SSL) telah aktif dan berfungsi dengan sempurna.\n\nWaktu Kirim: " . date('Y-m-d H:i:s');

        try {
            Mail::raw($body, function ($message) use ($toEmail, $subject) {
                $message->to($toEmail)
                        ->subject($subject);
            });

            return response()->json([
                'success' => true,
                'message' => "Email uji coba berhasil dikirim ke {$toEmail}!",
            ]);
        } catch (\Exception $e) {
            Log::error('Test SMTP Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }
}
