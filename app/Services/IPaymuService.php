<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IPaymuService
{
    protected string $va;
    protected string $apiKey;
    protected bool $isProduction;
    protected string $apiUrl;

    public function __construct()
    {
        $this->va = (string) (config('services.ipaymu.va') ?? '');
        $this->apiKey = (string) (config('services.ipaymu.api_key') ?? '');
        $this->isProduction = (bool) (config('services.ipaymu.is_production') ?? false);
        $this->apiUrl = (string) (config('services.ipaymu.api_url') ?: ($this->isProduction 
            ? 'https://my.ipaymu.com/api/v2' 
            : 'https://sandbox.ipaymu.com/api/v2'));
    }

    /**
     * Generate Signature for iPaymu API v2.
     * Formula: HMAC-SHA256(Method + ":" + VA + ":" + SHA256(Body) + ":" + APIKey, APIKey)
     */
    public function generateSignature(string $method, string $va, string $bodyHash, string $apiKey): string
    {
        $stringToSign = strtoupper($method) . ':' . $va . ':' . strtolower($bodyHash) . ':' . $apiKey;
        return hash_hmac('sha256', $stringToSign, $apiKey);
    }

    /**
     * Create payment link / session via iPaymu API v2 /payment endpoint.
     */
    public function createPaymentLink(array $transactionDetails, array $customerDetails): array
    {
        $products = [];
        $qtys = [];
        $prices = [];
        $descriptions = [];

        foreach ($transactionDetails['line_items'] ?? [] as $item) {
            $products[] = $item['name'] ?? 'Tiket Event';
            $qtys[] = (int) ($item['quantity'] ?? 1);
            $prices[] = (int) ($item['price'] ?? 0);
            $descriptions[] = $item['description'] ?? ($item['name'] ?? 'Tiket Event');
        }

        if (empty($products)) {
            $products[] = 'Pembayaran Tiket';
            $qtys[] = 1;
            $prices[] = (int) ($transactionDetails['amount'] ?? 0);
            $descriptions[] = 'Pembayaran Tiket';
        }

        $body = [
            'product'     => $products,
            'qty'         => $qtys,
            'price'       => $prices,
            'description' => $descriptions,
            'returnUrl'   => $transactionDetails['callback_url'] ?? url('/'),
            'notifyUrl'   => $transactionDetails['notify_url'] ?? route('ipaymu.notification'),
            'cancelUrl'   => $transactionDetails['failed_url'] ?? url('/'),
            'referenceId' => $transactionDetails['invoice_number'],
            'buyerName'   => $customerDetails['name'] ?? '',
            'buyerEmail'  => $customerDetails['email'] ?? '',
            'buyerPhone'  => $customerDetails['phone'] ?? '',
        ];

        // Simulation fallback if VA or API Key is missing in environment
        if (empty($this->va) || empty($this->apiKey)) {
            Log::warning('IPaymuService: IPAYMU_VA or IPAYMU_API_KEY is not configured in .env file.');
            return [
                'success'      => true,
                'payment_url'  => route('checkout.success', $transactionDetails['invoice_number']),
                'reference'    => 'IPAYMU-SIM-' . Str::upper(Str::random(10)),
                'is_simulated' => true,
            ];
        }

        $bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES);
        $bodyHash = strtolower(hash('sha256', $bodyJson));
        $signature = $this->generateSignature('POST', $this->va, $bodyHash, $this->apiKey);

        try {
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'va'           => $this->va,
                'signature'    => $signature,
                'timestamp'    => date('YmdHis'),
            ])
            ->withBody($bodyJson, 'application/json')
            ->post($this->apiUrl . '/payment');

            $data = $response->json();

            if ($response->successful() && isset($data['Status']) && (int)$data['Status'] === 200 && !empty($data['Data']['Url'])) {
                return [
                    'success'     => true,
                    'payment_url' => $data['Data']['Url'],
                    'session_id'  => $data['Data']['SessionID'] ?? '',
                    'reference'   => $data['Data']['PaymentNo'] ?? '',
                ];
            }

            $errorMessage = $data['Message'] ?? ($response->body() ?: 'iPaymu API request failed');

            if ($errorMessage === 'Invalid IP') {
                $errorMessage = 'IP/Domain server belum terdaftar/di-whitelist di Dashboard iPaymu (Menu Integrasi -> Validasi IP & Domain).';
            } elseif ($errorMessage === 'unauthorized credential') {
                $errorMessage = 'Autentikasi gagal. Pastikan VA & API Key cocok dengan environment (Sandbox vs Live Production) dan set IPAYMU_IS_PRODUCTION di file .env sesuai akun Anda.';
            }

            Log::error('IPaymu API request failed', [
                'url'             => $this->apiUrl . '/payment',
                'response_status' => $response->status(),
                'response_body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'iPaymu Error: ' . $errorMessage,
            ];
        } catch (\Exception $e) {
            Log::error('IPaymu API exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke iPaymu: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Map iPaymu status code to internal status & description.
     *
     * Status Codes:
     *  0  : Pending (Menunggu Pembayaran)
     *  1  : Success (Berhasil)
     *  2  : Cancelled (Dibatalkan)
     *  3  : Refund (Dikembalikan)
     *  4  : Error
     *  5  : Failed (Gagal)
     *  6  : Success - Unsettled (Berhasil Belum Settlement)
     *  7  : Escrow
     * -2  : Expired (Kedaluwarsa)
     * -1  : Expired / Failed (Legacy)
     */
    public static function parseStatusCode(int|string $code, string $paidStatus = '', string $statusDesc = ''): array
    {
        $codeInt = (int) $code;
        $paidStatus = strtolower(trim($paidStatus));
        $statusDesc = trim($statusDesc);

        // Status code text mapping based on iPaymu Documentation
        $statusMap = [
            0  => 'Pending (Menunggu Pembayaran)',
            1  => 'Success (Berhasil)',
            2  => 'Cancelled (Dibatalkan)',
            3  => 'Refund (Dikembalikan)',
            4  => 'Error',
            5  => 'Failed (Gagal)',
            6  => 'Success - Unsettled (Berhasil Belum Settlement)',
            7  => 'Escrow',
            -2 => 'Expired (Kedaluwarsa)',
            -1 => 'Expired / Failed',
        ];

        $description = $statusDesc ?: ($statusMap[$codeInt] ?? 'Unknown Status');

        // Check if status is PAID / SUCCESS: 1 (Success), 6 (Success Unsettled), 7 (Escrow)
        $isPaid = in_array($codeInt, [1, 6, 7]) 
            || $paidStatus === 'paid' 
            || in_array(strtolower($statusDesc), ['berhasil', 'success', 'paid', 'settlement']);

        // Check if status is FAILED / CANCELLED / EXPIRED: -2 (Expired), 2 (Cancelled), 3 (Refund), 4 (Error), 5 (Failed)
        $isFailed = in_array($codeInt, [-2, -1, 2, 3, 4, 5]) 
            || in_array(strtolower($statusDesc), ['expired', 'cancelled', 'dibatalkan', 'gagal', 'failed', 'kedaluwarsa', 'batal']);

        // Map to internal database transaction payment_status
        if ($isPaid) {
            $internalStatus = 'paid';
        } elseif ($codeInt === 3 || strtolower($statusDesc) === 'refund') {
            $internalStatus = 'refunded';
        } elseif ($codeInt === -2 || in_array(strtolower($statusDesc), ['expired', 'kedaluwarsa'])) {
            $internalStatus = 'expired';
        } elseif ($isFailed) {
            $internalStatus = 'failed';
        } else {
            $internalStatus = 'pending';
        }

        return [
            'status_code'     => $codeInt,
            'status_text'     => $description,
            'status_desc'     => $description,
            'internal_status' => $internalStatus,
            'is_paid'         => $isPaid,
            'is_failed'       => $isFailed,
            'is_pending'      => ($internalStatus === 'pending'),
        ];
    }

    /**
     * Check transaction status via iPaymu API v2 /transaction endpoint.
     * POST {{baseUrl}}/api/v2/transaction
     */
    public function checkTransactionStatus(string|int $transactionId): array
    {
        if (empty($this->va) || empty($this->apiKey)) {
            Log::warning('IPaymuService: IPAYMU_VA or IPAYMU_API_KEY is not configured in .env file.');
            return [
                'success'         => false,
                'status_code'     => 0,
                'status'          => 'Pending',
                'status_desc'     => 'Credentials not configured',
                'internal_status' => 'pending',
                'is_paid'         => false,
                'is_failed'       => false,
                'is_simulated'    => true,
            ];
        }

        $body = [
            'transactionId' => is_numeric($transactionId) ? (int) $transactionId : (string) $transactionId,
        ];

        $bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES);
        $bodyHash = strtolower(hash('sha256', $bodyJson));
        $signature = $this->generateSignature('POST', $this->va, $bodyHash, $this->apiKey);

        try {
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'va'           => $this->va,
                'signature'    => $signature,
                'timestamp'    => date('YmdHis'),
            ])
            ->withBody($bodyJson, 'application/json')
            ->post($this->apiUrl . '/transaction');

            $data = $response->json();

            if ($response->successful() && isset($data['Status']) && (int)$data['Status'] === 200 && !empty($data['Data'])) {
                $statusData = $data['Data'] ?? [];
                $statusCode = isset($statusData['Status']) ? (int) $statusData['Status'] : 0;
                $statusDesc = (string) ($statusData['StatusDesc'] ?? '');
                $paidStatus = (string) ($statusData['PaidStatus'] ?? '');

                $parsed = self::parseStatusCode($statusCode, $paidStatus, $statusDesc);

                return array_merge([
                    'success'        => true,
                    'transaction_id' => $statusData['TransactionId'] ?? null,
                    'session_id'     => $statusData['SessionId'] ?? null,
                    'reference_id'   => $statusData['ReferenceId'] ?? null,
                    'data'           => $statusData,
                ], $parsed);
            }

            Log::warning('iPaymu check transaction status returned non-200 or error', [
                'transaction_id' => $transactionId,
                'response'       => $data,
            ]);

            return [
                'success' => false,
                'message' => $data['Message'] ?? 'Gagal mengecek status transaksi di iPaymu.',
                'raw'     => $data,
            ];
        } catch (\Exception $e) {
            Log::error('iPaymu check transaction status exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
