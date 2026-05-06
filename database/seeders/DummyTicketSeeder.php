<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\TicketCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DummyTicketSeeder extends Seeder
{
    public function run()
    {
        $categories = TicketCategory::all();

        if ($categories->isEmpty()) {
            $this->command->info('No ticket categories found. Please create events and categories first.');
            return;
        }

        foreach ($categories as $category) {
            $this->command->info("Generating tickets for category: {$category->name} (Event: {$category->event->name})");

            // Generate 50 'sold' tickets (for testing redemption)
            for ($i = 1; $i <= 25; $i++) {
                $this->createDummyTicket($category, 'sold');
            }

            // Generate 50 'redeemed' tickets (for testing gate scan)
            for ($i = 1; $i <= 25; $i++) {
                $this->createDummyTicket($category, 'redeemed');
            }
            
            $this->command->info("Created 50 tickets for {$category->name}");
        }
    }

    private function createDummyTicket($category, $status)
    {
        return DB::transaction(function () use ($category, $status) {
            $transaction = Transaction::create([
                'tenant_id' => $category->tenant_id,
                'event_id' => $category->event_id,
                'ticket_category_id' => $category->id,
                'quantity' => 1,
                'reference_no' => 'TRX-' . strtoupper(Str::random(10)),
                'customer_name' => 'Dummy Visitor ' . Str::random(5),
                'customer_email' => 'dummy' . Str::random(5) . '@example.com',
                'customer_phone' => '08' . rand(100000000, 999999999),
                'customer_nik' => '320' . rand(1000000000000, 9999999999999),
                'total_amount' => $category->price,
                'payment_status' => 'paid',
                'payment_method' => 'dummy',
                'channel' => 'pos',
                'paid_at' => now(),
            ]);

            $ticketData = [
                'tenant_id' => $category->tenant_id,
                'event_id' => $category->event_id,
                'transaction_id' => $transaction->id,
                'ticket_category_id' => $category->id,
                'ticket_code' => 'EV-' . strtoupper(Str::random(10)),
                'status' => $status,
            ];

            if ($status === 'redeemed') {
                $ticketData['wristband_qr'] = 'WB-' . strtoupper(Str::random(10));
                $ticketData['redeemed_at'] = now();
                // Pick a user to be the redeemer (usually an admin or staff)
                $ticketData['redeemed_by'] = 1; 
            }

            $ticket = Ticket::create($ticketData);
            $category->increment('sold_count');
            
            return $ticket;
        });
    }
}
