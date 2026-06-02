<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $sessions = DB::table('sessions')->orderBy('last_activity', 'desc')->limit(5)->get();
    echo "Last 5 sessions:\n";
    foreach ($sessions as $s) {
        echo "ID: " . $s->id . " | User ID: " . ($s->user_id ?? 'NULL') . " | IP: " . $s->ip_address . " | Last Activity: " . date('Y-m-d H:i:s', $s->last_activity) . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
