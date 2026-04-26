<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach(App\Models\Event::all() as $e) {
    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $e->update(['security_code' => $code]);
    echo "Event: {$e->name} -> Code: {$code}\n";
}
echo "Done!\n";
