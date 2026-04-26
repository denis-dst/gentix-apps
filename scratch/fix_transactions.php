<?php

use App\Models\Transaction;
use App\Models\Ticket;
use Illuminate\Support\Str;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find pending transactions
$transactions = Transaction::where('payment_status', 'pending')->get();

foreach ($transactions as $transaction) {
    echo "Processing Transaction: {$transaction->reference_no}\n";
    
    // Update Transaction
    $transaction->update([
        'payment_status' => 'paid',
        'paid_at' => now(),
        'payment_method' => 'bank_transfer' // Sample method
    ]);

    // Create Tickets
    for ($i = 0; $i < $transaction->quantity; $i++) {
        $ticket = Ticket::create([
            'tenant_id' => $transaction->tenant_id,
            'event_id' => $transaction->event_id,
            'transaction_id' => $transaction->id,
            'ticket_category_id' => $transaction->ticket_category_id,
            'ticket_code' => 'GTX-' . strtoupper(Str::random(10)),
            'status' => 'sold',
        ]);
        echo "Created Ticket: {$ticket->ticket_code}\n";
    }

    // Increment sold count
    if ($transaction->category) {
        $transaction->category->increment('sold_count', $transaction->quantity);
    }
}

echo "Done!\n";
