<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PaylabsService
{
    public function createPaymentLink(array $payload): array
    {
        return $this->post(config('services.paylabs.h5_endpoint'), $payload);
    }

    public function post(string $endpoint, array $payload): array
    {
        $this->ensureConfigured();

        $payload = $this->removeNullValues($payload);
        $body = $this->encodeBody($payload);
        $timestamp = now(config('services.paylabs.timezone', 'Asia/Jakarta'))->format('Y-m-d\TH:i:sP');
        $requestId = $payload['requestId'] ?? $this->makeRequestId();
        $signature = $this->sign('POST', $endpoint, $body, $timestamp);

        $response = Http::withBody($body, 'application/json;charset=utf-8')
            ->withHeaders([
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signature,
                'X-PARTNER-ID' => config('services.paylabs.partner_id'),
                'X-REQUEST-ID' => $requestId,
            ])
            ->timeout(config('services.paylabs.timeout', 30))
            ->post(rtrim(config('services.paylabs.base_url'), '/') . $endpoint);

        return [
            'request_id' => $requestId,
            'signature' => $signature,
            'response' => $response,
            'body' => $this->decodeResponse($response),
        ];
    }

    public function sign(string $method, string $endpoint, string $body, string $timestamp): string
    {
        $privateKey = $this->readKey(config('services.paylabs.private_key_path'), 'private');
        $stringToSign = $this->stringToSign($method, $endpoint, $body, $timestamp);

        if (!openssl_sign($stringToSign, $signatureRaw, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Unable to sign Paylabs request.');
        }

        return base64_encode($signatureRaw);
    }

    public function verify(string $method, string $endpoint, string $body, string $timestamp, string $signature): bool
    {
        $publicKey = $this->readKey(config('services.paylabs.public_key_path'), 'public');
        $stringToSign = $this->stringToSign($method, $endpoint, $this->minifyBody($body), $timestamp);
        $signatureRaw = base64_decode($signature, true);

        if ($signatureRaw === false) {
            return false;
        }

        return openssl_verify($stringToSign, $signatureRaw, $publicKey, OPENSSL_ALGO_SHA256) === 1;
    }

    public function signedResponse(array $payload, string $endpoint, ?string $requestId = null): array
    {
        $this->ensureConfigured();

        $payload = $this->removeNullValues($payload);
        $body = $this->encodeBody($payload);
        $timestamp = now(config('services.paylabs.timezone', 'Asia/Jakarta'))->format('Y-m-d\TH:i:sP');
        $signature = $this->sign('POST', $endpoint, $body, $timestamp);

        return [
            'body' => $payload,
            'body_content' => $body,
            'headers' => [
                'Content-Type' => 'application/json;charset=utf-8',
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signature,
                'X-PARTNER-ID' => config('services.paylabs.partner_id'),
                'X-REQUEST-ID' => $requestId ?: ($payload['requestId'] ?? $this->makeRequestId()),
            ],
        ];
    }

    public function makeRequestId(): string
    {
        return 'REQ-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(8));
    }

    public function minifyBody(string $body): string
    {
        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return $body;
        }

        return $this->encodeBody($this->removeNullValues($decoded));
    }

    private function stringToSign(string $method, string $endpoint, string $body, string $timestamp): string
    {
        return strtoupper($method) . ':' . $endpoint . ':' . hash('sha256', $body) . ':' . $timestamp;
    }

    private function encodeBody(array $payload): string
    {
        return json_encode($payload, JSON_UNESCAPED_SLASHES);
    }

    private function decodeResponse(Response $response): array
    {
        $json = $response->json();

        return is_array($json) ? $json : ['raw' => $response->body()];
    }

    private function readKey(?string $path, string $type): string
    {
        if ($path && !str_starts_with($path, '/') && !preg_match('/^[A-Za-z]:[\/\\\\]/', $path)) {
            $path = base_path($path);
        }

        if (!$path || !is_file($path)) {
            throw new RuntimeException("Paylabs {$type} key file is not configured or cannot be read.");
        }

        $key = file_get_contents($path);

        if (!$key) {
            throw new RuntimeException("Paylabs {$type} key file is empty.");
        }

        return $key;
    }

    private function ensureConfigured(): void
    {
        foreach (['base_url', 'merchant_id', 'partner_id'] as $key) {
            if (!config("services.paylabs.{$key}")) {
                throw new RuntimeException("Paylabs {$key} is not configured.");
            }
        }
    }

    private function removeNullValues(array $payload): array
    {
        return collect($payload)
            ->reject(fn ($value) => $value === null)
            ->map(fn ($value) => is_array($value) ? $this->removeNullValues($value) : $value)
            ->all();
    }
}
