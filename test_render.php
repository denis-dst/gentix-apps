<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$event = App\Models\Event::find(3);
echo view('organizer.events.edit', ['event' => $event])->withErrors(new \Illuminate\Support\MessageBag)->render();
