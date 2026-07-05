<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain');

// Reproduce exactly what DokuService does
$clientId   = config('services.doku.client_id') ?? '';
$sharedKey  = config('services.doku.shared_key') ?? '';
$targetPath = '/checkout/v1/payment';
$requestId  = 'REQ-debug-0001';
$timestamp  = '2026-01-01T00:00:00Z';

$body = [
    'order' => [
        'amount' => 500,
        'invoice_number' => 'TX-DEBUG-001',
        'callback_url' => 'http://gentix-apps.test/checkout/success/TX-DEBUG-001',
        'line_items' => [
            ['id' => 1, 'price' => 500, 'quantity' => 1, 'name' => 'Test Ticket']
        ],
    ],
    'customer' => [
        'name'  => 'Test User',
        'email' => 'test@example.com',
        'phone' => '6281234567890',
    ],
];

$bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$digest   = base64_encode(hash('sha256', $bodyJson, true));

$rawString = "Client-Id:"       . $clientId   . "\n" .
             "Request-Id:"      . $requestId  . "\n" .
             "Request-Timestamp:" . $timestamp . "\n" .
             "Request-Target:"  . $targetPath . "\n" .
             "Digest:"          . $digest;

$signatureBin  = hash_hmac('sha256', $rawString, $sharedKey, true);
$signatureB64  = base64_encode($signatureBin);

echo "=== Input ===\n";
echo "Client-Id:    {$clientId}\n";
echo "Request-Id:   {$requestId}\n";
echo "Timestamp:    {$timestamp}\n";
echo "Target:       {$targetPath}\n";
echo "\n=== Body JSON ===\n";
echo $bodyJson . "\n";
echo "\n=== Digest ===\n";
echo $digest . "\n";
echo "\n=== Raw Signature String ===\n";
echo $rawString . "\n";
echo "\n=== HMAC Key (hex) ===\n";
echo bin2hex($sharedKey) . "\n";
echo "\n=== Signature (base64) ===\n";
echo "HMACSHA256=" . $signatureB64 . "\n";
echo "\n=== Key length ===\n";
echo strlen($sharedKey) . " chars\n";
echo "\n=== Key raw ===\n";
echo "'{$sharedKey}'\n";
