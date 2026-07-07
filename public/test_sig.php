<?php
// Quick end-to-end test using actual .env credentials + correct body format
$dotenv = parse_ini_file(__DIR__ . '/../.env');
$clientId  = trim($dotenv['DOKU_CLIENT_ID'], '"\'');
$sharedKey = trim($dotenv['DOKU_SHARED_KEY'], '"\'');
$apiUrl    = 'https://api-sandbox.doku.com';
$targetPath = '/checkout/v1/payment';
$requestId  = 'REQ-final-test-' . substr(md5(microtime()), 0, 8);

header('Content-Type: text/plain');

// Get Doku server time
$ctx = stream_context_create(['http' => ['method' => 'HEAD', 'timeout' => 5]]);
@file_get_contents($apiUrl, false, $ctx);
$serverDate = null;
foreach ($http_response_header ?? [] as $h) {
    if (stripos($h, 'Date:') === 0) {
        $serverDate = trim(substr($h, 5));
    }
}
$timestamp = $serverDate ? gmdate('Y-m-d\TH:i:s\Z', strtotime($serverDate)) : gmdate('Y-m-d\TH:i:s\Z');

echo "Client-Id: $clientId\n";
echo "Key length: " . strlen($sharedKey) . " chars\n";
echo "Timestamp: $timestamp\n\n";

// Correct body: id as string, includes payment object
$body = [
    'order' => [
        'amount'         => 10000,
        'invoice_number' => 'TX-TEST-' . strtoupper(substr(md5(microtime()), 0, 6)),
        'callback_url'   => 'http://gentix-apps.test/checkout/success/TX-TEST',
        'failed_url'     => 'http://gentix-apps.test/',
        'line_items'     => [
            ['id' => '21', 'price' => 10000, 'quantity' => 1, 'name' => 'Test Gate - Test Event']
        ],
    ],
    'payment' => [
        'payment_due_date' => 60,
    ],
    'customer' => [
        'name'  => 'Test User',
        'email' => 'test@example.com',
        'phone' => '6281234567890',
    ],
];

$bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$digest   = base64_encode(hash('sha256', $bodyJson, true));

$rawString = "Client-Id:$clientId\nRequest-Id:$requestId\nRequest-Timestamp:$timestamp\nRequest-Target:$targetPath\nDigest:$digest";
$signature = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $rawString, $sharedKey, true));

echo "Body:\n$bodyJson\n\n";

// Make request using file_get_contents (no curl needed)
$opts = [
    'http' => [
        'method'  => 'POST',
        'header'  => implode("\r\n", [
            'Content-Type: application/json',
            "Client-Id: $clientId",
            "Request-Id: $requestId",
            "Request-Timestamp: $timestamp",
            "Signature: $signature",
            "Digest: $digest",
        ]),
        'content' => $bodyJson,
        'timeout' => 20,
        'ignore_errors' => true,
    ],
];
$ctx = stream_context_create($opts);
$result = file_get_contents($apiUrl . $targetPath, false, $ctx);

// Get HTTP status
$status = 'unknown';
foreach ($http_response_header ?? [] as $h) {
    if (preg_match('/HTTP\/\S+ (\d+)/', $h, $m)) {
        $status = $m[1];
    }
}

echo "HTTP Status: $status\n";
echo "Response:\n";
echo json_encode(json_decode($result), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
