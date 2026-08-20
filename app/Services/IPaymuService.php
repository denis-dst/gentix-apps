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
     * Check transaction status via iPaymu API v2 /transaction endpoint.
     */
    public function checkTransactionStatus(string $transactionId): array
    {
        if (empty($this->va) || empty($this->apiKey)) {
            return [
                'success' => true,
                'status_code' => 1,
                'status' => 'Berhasil',
                'is_simulated' => true,
            ];
        }

        $body = [
            'transactionId' => $transactionId,
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

            if ($response->successful() && isset($data['Status']) && (int)$data['Status'] === 200) {
                $statusData = $data['Data'] ?? [];
                $statusCode = (int) ($statusData['StatusCode'] ?? $statusData['Status'] ?? 0);
                return [
                    'success' => true,
                    'status_code' => $statusCode,
                    'status' => $statusData['Status'] ?? '',
                    'data' => $statusData,
                ];
            }

            return [
                'success' => false,
                'message' => $data['Message'] ?? 'Gagal mengecek status transaksi',
            ];
        } catch (\Exception $e) {
            Log::error('iPaymu check transaction status exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
