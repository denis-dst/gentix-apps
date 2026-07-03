<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class DokuService
{
    protected string $clientId;
    protected string $sharedKey;
    protected bool $isProduction;
    protected string $apiUrl;

    public function __construct()
    {
        $this->clientId = config('services.doku.client_id', '');
        $this->sharedKey = config('services.doku.shared_key', '');
        $this->isProduction = config('services.doku.is_production', false);
        $this->apiUrl = config('services.doku.api_url') ?: ($this->isProduction 
            ? 'https://api.doku.com' 
            : 'https://api-sandbox.doku.com');
    }

    /**
     * Generate the payment URL / checkout session using Doku API.
     */
    public function createPaymentLink(array $transactionDetails, array $customerDetails): array
    {
        $this->ensureConfigured();

        // Target path for Doku hosted checkout
        $targetPath = '/checkout/v1/payment';
        $requestId = 'REQ-' . Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        $body = [
            'order' => [
                'amount' => (int) $transactionDetails['amount'],
                'invoice_number' => $transactionDetails['invoice_number'],
                'callback_url' => $transactionDetails['callback_url'] ?? url('/'),
                'line_items' => $transactionDetails['line_items'] ?? [],
            ],
            'customer' => [
                'name' => $customerDetails['name'],
                'email' => $customerDetails['email'],
                'phone' => $customerDetails['phone'],
            ]
        ];

        $signature = $this->generateSignature($targetPath, $requestId, $timestamp, $body);

        // For now during preparation, return simulation URLs if credentials are empty
        if (empty($this->clientId) || empty($this->sharedKey)) {
            return [
                'success' => true,
                'payment_url' => route('checkout.success', $transactionDetails['invoice_number']),
                'reference' => 'DOKU-SIM-' . Str::upper(Str::random(10)),
                'is_simulated' => true,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => 'HMACSHA256=' . $signature,
            ])
            ->post($this->apiUrl . $targetPath, $body);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'payment_url' => $data['response']['payment']['url'] ?? '',
                    'reference' => $data['response']['payment']['reference_number'] ?? '',
                ];
            }

            return [
                'success' => false,
                'message' => 'Doku API Error: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Doku connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate Signature according to Doku API requirement.
     */
    public function generateSignature(string $targetPath, string $requestId, string $timestamp, array $body): string
    {
        $bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $digest = base64_encode(hash('sha256', $bodyJson, true));

        $rawString = "Client-Id:" . $this->clientId . "\n" .
                     "Request-Id:" . $requestId . "\n" .
                     "Request-Timestamp:" . $timestamp . "\n" .
                     "Request-Target:" . $targetPath . "\n" .
                     "Digest:" . $digest;

        $signature = hash_hmac('sha256', $rawString, $this->sharedKey, true);
        return base64_encode($signature);
    }

    /**
     * Verify the notification signature from Doku webhook.
     */
    public function verifyNotification(array $headers, array $body): bool
    {
        $this->ensureConfigured();

        $signatureHeader = $headers['signature'][0] ?? '';
        $requestId = $headers['request-id'][0] ?? '';
        $timestamp = $headers['request-timestamp'][0] ?? '';
        $targetPath = '/doku/notification'; // must match webhook path

        if (str_starts_with($signatureHeader, 'HMACSHA256=')) {
            $signatureHeader = substr($signatureHeader, 11);
        }

        $calculatedSignature = $this->generateSignature($targetPath, $requestId, $timestamp, $body);

        return hash_equals($calculatedSignature, $signatureHeader);
    }

    private function ensureConfigured(): void
    {
        // Warn if not properly configured but don't strictly crash during initial preparation setup
        if (empty($this->clientId) || empty($this->sharedKey)) {
            \Log::warning('Doku API Client ID or Shared Key is not set in environment.');
        }
    }
}
