<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GateLog;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RecurringEventGateScanTest extends TestCase
{
    use RefreshDatabase;

    protected User $gateUser;
    protected Tenant $tenant;
    protected Event $recurringEvent;
    protected TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Petugas Gate']);

        $this->tenant = Tenant::create([
            'name' => 'Kajian Rutin Tenant',
            'slug' => 'kajian-rutin-tenant',
            'email' => 'admin@kajianrutin.com',
            'status' => 'active',
        ]);

        $this->gateUser = User::create([
            'name' => 'Kru Gate 1',
            'email' => 'gate1@kajianrutin.com',
            'password' => bcrypt('secret123'),
            'tenant_id' => $this->tenant->id,
            'is_active' => true,
        ]);
        $this->gateUser->assignRole('Petugas Gate');

        $this->recurringEvent = Event::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Kajian Rutin Mingguan',
            'slug' => 'kajian-rutin-mingguan',
            'venue' => 'Masjid Raya',
            'city' => 'Jakarta',
            'event_start_date' => now()->subMonth(),
            'event_end_date' => now()->addYear(),
            'status' => 'published',
            'is_free' => false,
            'purchase_flow' => 'evoucher',
            'meta' => [
                'is_recurring' => true,
            ],
        ]);

        $this->category = TicketCategory::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'name' => 'Member Pass Rutin',
            'price' => 100000,
            'quota' => 500,
        ]);
    }

    public function test_recurring_event_allows_checkin_on_new_day_after_previous_week_checkin(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'ticket_category_id' => $this->category->id,
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'quantity' => 1,
            'total_amount' => 100000,
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'reference_no' => 'TRX-REC-001',
            'paid_at' => now()->subMonth(),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->category->id,
            'ticket_code' => 'GTX-BUDI-001',
            'status' => 'redeemed', // Already redeemed on week 1
            'redeemed_at' => now()->subDays(7),
        ]);

        // Week 1 Scan IN log (7 days ago)
        GateLog::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'ticket_id' => $ticket->id,
            'gate_name' => 'Gate Utama',
            'type' => 'IN',
            'scanned_at' => now()->subDays(7),
            'scanned_by' => $this->gateUser->id,
        ]);

        // Week 2: Scan IN today via API
        $response = $this->actingAs($this->gateUser, 'sanctum')->postJson('/api/gate/scan', [
            'wristband_qr' => $ticket->ticket_code,
            'ticket_code' => $ticket->ticket_code,
            'type' => 'IN',
            'gate_name' => 'Gate Utama',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'SUCCESS',
            'ticket_code' => 'GTX-BUDI-001',
        ]);

        // Verify new log created with today's timestamp
        $latestLog = GateLog::where('ticket_id', $ticket->id)->orderBy('scanned_at', 'desc')->first();
        $this->assertEquals('IN', $latestLog->type);
        $this->assertTrue($latestLog->scanned_at->isToday());
    }

    public function test_recurring_event_rejects_second_checkin_on_same_day(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'ticket_category_id' => $this->category->id,
            'customer_name' => 'Andi Wijaya',
            'customer_email' => 'andi@example.com',
            'customer_phone' => '081234567891',
            'quantity' => 1,
            'total_amount' => 100000,
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'reference_no' => 'TRX-REC-002',
            'paid_at' => now()->subDays(10),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->category->id,
            'ticket_code' => 'GTX-ANDI-002',
            'status' => 'sold',
        ]);

        // First scan IN today -> SUCCESS
        $firstScan = $this->actingAs($this->gateUser, 'sanctum')->postJson('/api/gate/scan', [
            'wristband_qr' => $ticket->ticket_code,
            'ticket_code' => $ticket->ticket_code,
            'type' => 'IN',
            'gate_name' => 'Gate Utama',
        ]);
        $firstScan->assertStatus(200);

        // Second scan IN today on the same day -> REJECT
        $secondScan = $this->actingAs($this->gateUser, 'sanctum')->postJson('/api/gate/scan', [
            'wristband_qr' => $ticket->ticket_code,
            'ticket_code' => $ticket->ticket_code,
            'type' => 'IN',
            'gate_name' => 'Gate Utama',
        ]);
        $secondScan->assertStatus(403);
        $secondScan->assertJson([
            'status' => 'REJECT',
        ]);
        $this->assertStringContainsString('hari ini', $secondScan->json('message'));
    }

    public function test_recurring_event_allows_out_and_re_in_on_same_day(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'ticket_category_id' => $this->category->id,
            'customer_name' => 'Citra Lestari',
            'customer_email' => 'citra@example.com',
            'customer_phone' => '081234567892',
            'quantity' => 1,
            'total_amount' => 100000,
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'reference_no' => 'TRX-REC-003',
            'paid_at' => now()->subDays(10),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->category->id,
            'ticket_code' => 'GTX-CITRA-003',
            'status' => 'sold',
        ]);

        // 1. Scan IN
        $inScan = $this->actingAs($this->gateUser, 'sanctum')->postJson('/api/gate/scan', [
            'wristband_qr' => $ticket->ticket_code,
            'ticket_code' => $ticket->ticket_code,
            'type' => 'IN',
            'gate_name' => 'Gate Utama',
        ]);
        $inScan->assertStatus(200);

        // 2. Scan OUT
        $outScan = $this->actingAs($this->gateUser, 'sanctum')->postJson('/api/gate/scan', [
            'wristband_qr' => $ticket->ticket_code,
            'ticket_code' => $ticket->ticket_code,
            'type' => 'OUT',
            'gate_name' => 'Gate Utama',
        ]);
        $outScan->assertStatus(200);

        // 3. Scan IN again
        $reInScan = $this->actingAs($this->gateUser, 'sanctum')->postJson('/api/gate/scan', [
            'wristband_qr' => $ticket->ticket_code,
            'ticket_code' => $ticket->ticket_code,
            'type' => 'IN',
            'gate_name' => 'Gate Utama',
        ]);
        $reInScan->assertStatus(200);
    }

    public function test_recurring_event_rejects_scan_when_event_is_expired(): void
    {
        $this->recurringEvent->update([
            'event_end_date' => now()->subDays(2),
        ]);

        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'ticket_category_id' => $this->category->id,
            'customer_name' => 'Dedi Kurniawan',
            'customer_email' => 'dedi@example.com',
            'customer_phone' => '081234567893',
            'quantity' => 1,
            'total_amount' => 100000,
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'reference_no' => 'TRX-REC-004',
            'paid_at' => now()->subMonth(),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->category->id,
            'ticket_code' => 'GTX-DEDI-004',
            'status' => 'sold',
        ]);

        $scan = $this->actingAs($this->gateUser, 'sanctum')->postJson('/api/gate/scan', [
            'wristband_qr' => $ticket->ticket_code,
            'ticket_code' => $ticket->ticket_code,
            'type' => 'IN',
            'gate_name' => 'Gate Utama',
        ]);

        $scan->assertStatus(403);
        $this->assertStringContainsString('telah berakhir', $scan->json('message'));
    }

    public function test_organizer_web_gate_supports_recurring_event_scan(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'ticket_category_id' => $this->category->id,
            'customer_name' => 'Eko Prasetyo',
            'customer_email' => 'eko@example.com',
            'customer_phone' => '081234567894',
            'quantity' => 1,
            'total_amount' => 100000,
            'payment_status' => 'paid',
            'payment_method' => 'qris',
            'reference_no' => 'TRX-REC-005',
            'paid_at' => now()->subMonth(),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->category->id,
            'ticket_code' => 'GTX-EKO-005',
            'status' => 'redeemed',
            'redeemed_at' => now()->subDays(7),
        ]);

        // Previous week scan log
        GateLog::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->recurringEvent->id,
            'ticket_id' => $ticket->id,
            'gate_name' => 'Main Gate',
            'type' => 'IN',
            'scanned_at' => now()->subDays(7),
            'scanned_by' => $this->gateUser->id,
        ]);

        // Organizer user
        $organizerUser = User::create([
            'name' => 'Admin Organizer',
            'email' => 'organizer@kajianrutin.com',
            'password' => bcrypt('password123'),
            'tenant_id' => $this->tenant->id,
            'is_active' => true,
        ]);
        $organizerUser->assignRole('Petugas Gate');

        // Setup session for gate
        $response = $this->actingAs($organizerUser)
            ->withSession([
                'gate_event_id' => $this->recurringEvent->id,
                'gate_name' => 'Main Gate',
                'gate_allowed_categories' => [],
            ])
            ->postJson(route('organizer.gate.process'), [
                'ticket_code' => $ticket->ticket_code,
                'event_id' => $this->recurringEvent->id,
                'mode' => 'IN',
                'gate_name' => 'Main Gate',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Berhasil Check-in',
        ]);
    }
}
