<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'batik@gentix.id';
$password = 'Bismillah134679!';

$user = \App\Models\User::where('email', $email)->first();
if ($user) {
    echo "User found!\n";
    echo "Email: " . $user->email . "\n";
    echo "Roles: " . implode(', ', $user->roles()->pluck('name')->toArray()) . "\n";
    $match = \Illuminate\Support\Facades\Hash::check($password, $user->password);
    echo "Password match: " . ($match ? "YES" : "NO") . "\n";
} else {
    echo "User NOT found!\n";
}
