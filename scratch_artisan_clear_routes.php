<?php
echo "<pre>\n";
try {
    $base = dirname(__DIR__);
    require $base . '/vendor/autoload.php';
    $app = require_once $base . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "Running route:clear...\n";
    Artisan::call('route:clear');
    echo Artisan::output() . "\n";
    
    echo "Running config:clear...\n";
    Artisan::call('config:clear');
    echo Artisan::output() . "\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "</pre>";
@unlink(__FILE__);
