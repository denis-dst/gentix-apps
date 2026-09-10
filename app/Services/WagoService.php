<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

// Ensure WAGO class exists even if composer vendor was not updated on production
if (!class_exists(\Wago\Wago::class)) {
    $fallbackFile = __DIR__ . '/Wago/Wago.php';
    if (file_exists($fallbackFile)) {
        require_once $fallbackFile;
    }
}

class WagoService
{
    protected string $appId;
    protected string $apiKey;
    protected string $callbackSecret;
    protected bool $isProduction;
    protected bool $isSandbox;
    protected string $apiBaseUrl;
    protected string $checkoutUrl;
    protected $wagoSdk = null;

    public function __construct()
    {
        $this->appId          = (string) (config('services.wago.app_id') ?? 'GENTIXAPPS');
        $this->apiKey         = (string) (config('services.wago.api_key') ?? '');
        $this->callbackSecret = (string) (config('services.wago.callback_secret') ?? '');
        $this->isProduction   = (bool) (config('services.wago.is_production') ?? true);
        $this->isSandbox      = (bool) (config('services.wago.is_sandbox') ?? false);
        $this->apiBaseUrl     = (string) (config('services.wago.api_base_url') ?: 'https://api.wago-id.web.id');
        $this->checkoutUrl    = (string) (config('services.wago.checkout_url') ?: 'https://pay.wago-id.web.id/checkout');

        if (!empty($this->appId) && !empty($this->apiKey)) {
            if (class_exists(\Wago\Wago::class)) {
                try {
                    $this->wagoSdk = new \Wago\Wago([
                        'appId'         => $this->appId,
                        'apiKey'        => $this->apiKey,
                        'webhookSecret' => $this->callbackSecret,
                        'isProduction'  => $this->isProduction,
                    ]);
                } catch (Throwable $e) {
                    Log::warning('WagoService: Failed to initialize WAGO SDK: ' . $e->getMessage() . '. Using native HTTP client.');
                    $this->wagoSdk = null;
                }
            } else {
                Log::info('WagoService: Wago class not found in autoloader, using native Laravel HTTP client.');
            }
        }
    }

    /**
     * Create payment session / order via WAGO Pay-Engine.
     *
     * @param array $transactionDetails Keys: order_id (string), nominal (int), callback_url (optional), payment_method (optional)
     * @param array $customerDetails    Keys: name, email, phone
     * @return array
     */
    public function createPayment(array $transactionDetails, array $customerDetails): array
    {
        $orderId = (string) ($transactionDetails['order_id'] ?? $transactionDetails['invoice_number'] ?? '');
        $nominal = (int) ($transactionDetails['nominal'] ?? $transactionDetails['amount'] ?? 0);

        if (empty($orderId) || $nominal <= 0) {
            return [
                'success' => false,
                'message' => 'Order ID dan nominal tagihan tidak valid.',
            ];
        }

        // Simulation fallback if API Key is not configured in local environment
        if (empty($this->apiKey)) {
            Log::warning('WagoService: WAGO_API_KEY is not configured in .env file. Using simulated checkout.');
            return [
                'success'      => true,
                'payment_url'  => route('checkout.success', $orderId),
                'order_token'  => 'WAGO-SIM-' . Str::random(12),
                'order_id'     => $orderId,
                'is_simulated' => true,
            ];
        }

        $payload = [
            'order_id'        => $orderId,
            'nominal'         => $nominal,
            'customer_name'   => (string) ($customerDetails['name'] ?? ''),
            'customer_email'  => (string) ($customerDetails['email'] ?? ''),
            'customer_phone'  => (string) ($customerDetails['phone'] ?? ''),
            'payment_method'  => $transactionDetails['payment_method'] ?? 'QRIS',
            'callback_url'    => $transactionDetails['callback_url'] ?? route('wago.notification'),
        ];

        if ($this->isSandbox || !empty($transactionDetails['is_sandbox'])) {
            $payload['is_sandbox'] = true;
        }

        try {
            Log::info('WagoService: Creating transaction', [
                'order_id' => $orderId,
                'nominal'  => $nominal,
                'app_id'   => $this->appId,
            ]);

            // Attempt via official SDK
            $response = null;
            if ($this->wagoSdk) {
                $response = $this->wagoSdk->createTransaction($payload);
            } else {
                // Direct HTTP Fallback
                $res = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'x-api-key'    => $this->apiKey,
                ])->post(rtrim($this->apiBaseUrl, '/') . '/api/order', array_merge(['app_id' => $this->appId], $payload));

                $response = $res->json();
            }

            Log::info('WagoService: Create transaction response', ['response' => $response]);

            $status = $response['status'] ?? '';
            $data   = $response['data'] ?? [];

            if (($status === 'success' || isset($data['order_token'])) && !empty($data['order_token'])) {
                $orderToken = (string) $data['order_token'];
                // According to WAGO documentation:
                // Redirect URL: https://pay.wago-id.web.id/checkout/{order_id}?token={order_token}
                $paymentUrl = $data['checkout_url'] 
                    ?? $data['payment_url'] 
                    ?? (rtrim($this->checkoutUrl, '/') . '/' . urlencode($orderId) . '?token=' . urlencode($orderToken));

                return [
                    'success'      => true,
                    'payment_url'  => $paymentUrl,
                    'order_token'  => $orderToken,
                    'order_id'     => $orderId,
                    'nominal_unik' => $data['nominal_unik'] ?? $nominal,
                    'status'       => $data['status'] ?? 'PENDING',
                    'raw'          => $response,
                ];
            }

            $errorMessage = $response['message'] ?? $response['error'] ?? 'Gagal membuat invoice pembayaran di WAGO.';
            Log::error('WagoService: API returned error', ['response' => $response]);

            return [
                'success' => false,
                'message' => 'WAGO Error: ' . $errorMessage,
                'raw'     => $response,
            ];
        } catch (Exception $e) {
            Log::error('WagoService: Exception in createPayment', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke WAGO Payment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check transaction status directly from WAGO API.
     * Endpoint: GET /api/order?id={order_id}
     */
    public function checkTransactionStatus(string $orderId): array
    {
        if (empty($this->apiKey)) {
            Log::warning('WagoService: WAGO_API_KEY is not configured in .env file.');
            return [
                'success'         => false,
                'internal_status' => 'pending',
                'is_paid'         => false,
                'is_failed'       => false,
                'is_pending'      => true,
            ];
        }

        try {
            $data = null;
            if ($this->wagoSdk) {
                $data = $this->wagoSdk->getTransactionStatus($orderId);
            } else {
                $res = Http::withHeaders([
                    'x-api-key' => $this->apiKey,
                ])->get(rtrim($this->apiBaseUrl, '/') . '/api/order', [
                    'id' => $orderId,
                ]);
                $data = $res->json();
            }

            Log::info('WagoService: getTransactionStatus response', ['order_id' => $orderId, 'data' => $data]);

            if (!empty($data)) {
                $statusText = strtoupper(trim((string) ($data['status'] ?? '')));
                $isPaid     = ($statusText === 'SUCCESS' || $statusText === 'PAID');
                $isFailed   = in_array($statusText, ['FAILED', 'EXPIRED', 'CANCELLED']);

                $internalStatus = 'pending';
                if ($isPaid) {
                    $internalStatus = 'paid';
                } elseif ($statusText === 'EXPIRED') {
                    $internalStatus = 'expired';
                } elseif ($isFailed) {
                    $internalStatus = 'failed';
                }

                return [
                    'success'         => true,
                    'order_id'        => $data['order_id'] ?? $orderId,
                    'status'          => $statusText,
                    'internal_status' => $internalStatus,
                    'is_paid'         => $isPaid,
                    'is_failed'       => $isFailed,
                    'is_pending'      => ($internalStatus === 'pending'),
                    'nominal_unik'    => $data['nominal_unik'] ?? null,
                    'sn'              => $data['sn'] ?? null,
                    'paid_at'         => $data['paidAt'] ?? null,
                    'raw'             => $data,
                ];
            }

            return [
                'success' => false,
                'message' => 'Data status tidak ditemukan di server WAGO.',
            ];
        } catch (Exception $e) {
            Log::error('WagoService: Exception in checkTransactionStatus', [
                'order_id' => $orderId,
                'message'  => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify HMAC-SHA256 Webhook signature from WAGO.
     * Formula: HMAC-SHA256("{order_id}:{status}:{nominal_unik}:{sn}:{timestamp}", CallbackSecret)
     */
    public function verifyWebhookSignature(array $body, string $signature, string $timestamp): bool
    {
        // If Callback Secret is not yet configured, allow with warning log
        if (empty($this->callbackSecret)) {
            Log::warning('WagoService: WAGO_CALLBACK_SECRET is not configured in .env. Skipping signature verification.');
            return true;
        }

        if (empty($signature) || empty($timestamp)) {
            Log::warning('WagoService: Webhook missing signature or timestamp header.');
            return false;
        }

        try {
            if ($this->wagoSdk) {
                return $this->wagoSdk->verifyWebhook($body, $signature, $timestamp);
            }

            $orderId     = $body['order_id'] ?? '';
            $status      = $body['status'] ?? '';
            $nominalUnik = $body['nominal_unik'] ?? '';
            $sn          = $body['sn'] ?? '';

            $rawPayload        = "{$orderId}:{$status}:{$nominalUnik}:{$sn}:{$timestamp}";
            $expectedSignature = hash_hmac('sha256', $rawPayload, $this->callbackSecret);

            return hash_equals($expectedSignature, $signature);
        } catch (Exception $e) {
            Log::error('WagoService: Webhook verification error: ' . $e->getMessage());
            return false;
        }
    }
}
