<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::find(9);
if ($user) {
    echo "User 9 email: " . $user->email . "\n";
} else {
    echo "User 9 not found\n";
}
