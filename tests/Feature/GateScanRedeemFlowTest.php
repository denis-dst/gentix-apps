<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Gate;
use App\Models\GateLog;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GateScanRedeemFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $gateUser;
    protected Tenant $tenant;
    protected Event $redeemEvent;
    protected Event $directEvent;
    protected TicketCategory $redeemCategory;
    protected TicketCategory $directCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Petugas Gate']);

        $this->tenant = Tenant::create([
            'name' => 'Festival Musik',
            'slug' => 'festival-musik',
            'email' => 'admin@festivalmusik.com',
            'status' => 'active',
        ]);

        $this->gateUser = User::create([
            'name' => 'Operator Gate 1',
            'email' => 'operator@festivalmusik.com',
            'password' => bcrypt('password123'),
            'tenant_id' => $this->tenant->id,
            'is_active' => true,
        ]);
        $this->gateUser->assignRole('Petugas Gate');

        $this->redeemEvent = Event::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Konser Gelang 2026',
            'slug' => 'konser-gelang-2026',
            'venue' => 'Gelora Bung Karno',
            'city' => 'Jakarta',
            'event_start_date' => now()->addDays(2),
            'event_end_date' => now()->addDays(3),
            'status' => 'published',
            'is_free' => false,
            'purchase_flow' => 'redeem',
        ]);

        $this->redeemCategory = TicketCategory::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'name' => 'VIP Wristband',
            'price' => 500000,
            'quota' => 100,
        ]);

        $this->directEvent = Event::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Seminar Langsung 2026',
            'slug' => 'seminar-langsung-2026',
            'venue' => 'JCC Senayan',
            'city' => 'Jakarta',
            'event_start_date' => now()->addDays(5),
            'event_end_date' => now()->addDays(5),
            'status' => 'published',
            'is_free' => false,
            'purchase_flow' => 'evoucher',
        ]);

        $this->directCategory = TicketCategory::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->directEvent->id,
            'name' => 'General Pass',
            'price' => 150000,
            'quota' => 100,
        ]);
    }

    public function test_gate_scan_api_allows_unredeemed_wristband_scan_and_auto_redeems_without_buyer_data(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'customer_name' => 'Ahmad Pembeli',
            'customer_email' => 'ahmad@example.com',
            'customer_phone' => '08123456789',
            'reference_no' => 'INV-REDEEM-001',
            'quantity' => 1,
            'total_amount' => 500000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'ticket_code' => 'GTX-REDEEM-UNEXCHANGED',
            'wristband_qr' => null,
            'status' => 'sold',
        ]);

        $response = $this->actingAs($this->gateUser, 'sanctum')
            ->postJson('/api/gate/scan', [
                'wristband_qr' => $ticket->ticket_code,
                'type' => 'IN',
                'gate_name' => 'Gate 1',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'SUCCESS',
            'visitor' => '-',
            'email' => '-',
            'reference_no' => '-',
            'category' => 'VIP Wristband',
            'ticket_code' => 'GTX-REDEEM-UNEXCHANGED',
        ]);
        $this->assertStringNotContainsString('Ahmad Pembeli', $response->getContent());
        $this->assertStringNotContainsString('ahmad@example.com', $response->getContent());

        $ticket->refresh();
        $this->assertEquals('redeemed', $ticket->status);
        $this->assertNotNull($ticket->redeemed_at);
        $this->assertEquals('GTX-REDEEM-UNEXCHANGED', $ticket->wristband_qr);

        $this->assertDatabaseHas('gate_logs', [
            'ticket_id' => $ticket->id,
            'type' => 'IN',
            'gate_name' => 'Gate 1',
        ]);
    }

    public function test_gate_scan_api_allows_scan_when_wristband_preassigned_but_status_sold(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'customer_name' => 'Bambang Pembeli',
            'customer_email' => 'bambang@example.com',
            'customer_phone' => '08123456789',
            'reference_no' => 'INV-REDEEM-002',
            'quantity' => 1,
            'total_amount' => 500000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'ticket_code' => 'GTX-REDEEM-PREASSIGNED',
            'wristband_qr' => 'WB-VIP-0088',
            'status' => 'sold',
        ]);

        $response = $this->actingAs($this->gateUser, 'sanctum')
            ->postJson('/api/gate/scan', [
                'wristband_qr' => 'WB-VIP-0088',
                'type' => 'IN',
                'gate_name' => 'Gate 1',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'SUCCESS',
            'visitor' => '-',
            'email' => '-',
            'reference_no' => '-',
            'ticket_code' => 'WB-VIP-0088',
        ]);
        $this->assertStringNotContainsString('Bambang Pembeli', $response->getContent());

        $ticket->refresh();
        $this->assertEquals('redeemed', $ticket->status);
        $this->assertNotNull($ticket->redeemed_at);
        $this->assertEquals('WB-VIP-0088', $ticket->wristband_qr);
    }

    public function test_gate_scan_api_accepts_wristband_qr_and_omits_buyer_data_in_redeem_flow(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'customer_name' => 'Budi Pembeli Rahasia',
            'customer_email' => 'budi.rahasia@example.com',
            'customer_phone' => '08123456789',
            'reference_no' => 'INV-REDEEM-003',
            'quantity' => 1,
            'total_amount' => 500000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'ticket_code' => 'GTX-REDEEM-003',
            'wristband_qr' => 'WB-VIP-0099',
            'status' => 'redeemed',
            'redeemed_at' => now(),
        ]);

        $response = $this->actingAs($this->gateUser, 'sanctum')
            ->postJson('/api/gate/scan', [
                'wristband_qr' => 'WB-VIP-0099',
                'type' => 'IN',
                'gate_name' => 'Gate VIP',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'SUCCESS',
            'visitor' => '-',
            'email' => '-',
            'reference_no' => '-',
            'custom_question_label' => '-',
            'custom_question_answer' => '-',
            'category' => 'VIP Wristband',
            'ticket_code' => 'WB-VIP-0099',
        ]);
        $this->assertStringNotContainsString('Budi Pembeli', $response->getContent());
        $this->assertStringNotContainsString('budi.rahasia@example.com', $response->getContent());
    }

    public function test_gate_scan_api_does_not_trigger_group_checkin_in_redeem_flow(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'customer_name' => 'Keluarga Cemara',
            'customer_email' => 'cemara@example.com',
            'customer_phone' => '08123456789',
            'reference_no' => 'INV-GROUP-REDEEM',
            'quantity' => 3,
            'total_amount' => 1500000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $ticket1 = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'ticket_code' => 'GTX-GRP-1',
            'wristband_qr' => 'WB-VIP-0101',
            'status' => 'redeemed',
            'redeemed_at' => now(),
        ]);

        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'ticket_code' => 'GTX-GRP-2',
            'wristband_qr' => 'WB-VIP-0102',
            'status' => 'redeemed',
            'redeemed_at' => now(),
        ]);

        $response = $this->actingAs($this->gateUser, 'sanctum')
            ->postJson('/api/gate/scan', [
                'wristband_qr' => 'WB-VIP-0101',
                'type' => 'IN',
                'gate_name' => 'Gate VIP',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'SUCCESS',
            'category' => 'VIP Wristband',
            'visitor' => '-',
        ]);
        $this->assertNull($response->json('is_group'));
        $this->assertNull($response->json('attendees'));
    }

    public function test_gate_scan_api_anti_passback_omits_buyer_data_in_redeem_flow(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'customer_name' => 'Joni Pembeli',
            'customer_email' => 'joni@example.com',
            'customer_phone' => '08123456789',
            'reference_no' => 'INV-PASSBACK',
            'quantity' => 1,
            'total_amount' => 500000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'ticket_code' => 'GTX-PB-001',
            'wristband_qr' => 'WB-VIP-0555',
            'status' => 'redeemed',
            'redeemed_at' => now(),
        ]);

        GateLog::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'ticket_id' => $ticket->id,
            'gate_name' => 'Gate VIP',
            'type' => 'IN',
            'scanned_at' => now()->subMinutes(5),
            'scanned_by' => $this->gateUser->id,
        ]);

        $response = $this->actingAs($this->gateUser, 'sanctum')
            ->postJson('/api/gate/scan', [
                'wristband_qr' => 'WB-VIP-0555',
                'type' => 'IN',
                'gate_name' => 'Gate VIP',
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'REJECT',
            'visitor' => '-',
            'email' => '-',
            'reference_no' => '-',
        ]);
        $this->assertStringNotContainsString('Joni Pembeli', $response->json('message'));
        $this->assertStringContainsString('WB-VIP-0555', $response->json('message'));
    }

    public function test_gate_scan_api_returns_buyer_data_when_event_is_direct_flow(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->directEvent->id,
            'ticket_category_id' => $this->directCategory->id,
            'customer_name' => 'Citra Direct',
            'customer_email' => 'citra@example.com',
            'customer_phone' => '08123456789',
            'reference_no' => 'INV-DIRECT-001',
            'quantity' => 1,
            'total_amount' => 150000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->directEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->directCategory->id,
            'ticket_code' => 'GTX-DIRECT-001',
            'wristband_qr' => null,
            'status' => 'sold',
        ]);

        $response = $this->actingAs($this->gateUser, 'sanctum')
            ->postJson('/api/gate/scan', [
                'wristband_qr' => 'GTX-DIRECT-001',
                'type' => 'IN',
                'gate_name' => 'Gate Direct',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'SUCCESS',
            'visitor' => 'Citra Direct',
            'email' => 'citra@example.com',
            'reference_no' => 'INV-DIRECT-001',
            'category' => 'General Pass',
        ]);
    }

    public function test_gate_download_data_includes_unredeemed_tickets_and_omits_buyer_data(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'customer_name' => 'Pembeli Rahasia',
            'customer_email' => 'rahasia@example.com',
            'customer_phone' => '08123456789',
            'reference_no' => 'INV-DL-001',
            'quantity' => 2,
            'total_amount' => 1000000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        // 1 Redeemed ticket
        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'ticket_code' => 'GTX-DL-001',
            'wristband_qr' => 'WB-DL-0001',
            'status' => 'redeemed',
            'redeemed_at' => now(),
        ]);

        // 1 Unredeemed (sold) ticket
        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'ticket_code' => 'GTX-DL-002',
            'wristband_qr' => null,
            'status' => 'sold',
        ]);

        $response = $this->actingAs($this->gateUser, 'sanctum')
            ->getJson('/api/gate/download-data?event_id=' . $this->redeemEvent->id);

        $response->assertStatus(200);
        $tickets = collect($response->json('tickets'));
        $redeemedTicket = $tickets->firstWhere('wristband_qr', 'WB-DL-0001');
        $soldTicket = $tickets->firstWhere('ticket_code', 'GTX-DL-002');

        $this->assertNotNull($redeemedTicket);
        $this->assertEquals('-', $redeemedTicket['customer_name']);
        $this->assertEquals('-', $redeemedTicket['customer_email']);
        $this->assertEquals('-', $redeemedTicket['reference_no']);

        $this->assertNotNull($soldTicket);
        $this->assertEquals('-', $soldTicket['customer_name']);
        $this->assertEquals('-', $soldTicket['customer_email']);
        $this->assertEquals('-', $soldTicket['reference_no']);
    }

    public function test_organizer_gate_process_web_allows_unredeemed_wristband_scan_without_buyer_data(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'customer_name' => 'Doni Web',
            'customer_email' => 'doni@example.com',
            'customer_phone' => '08123456789',
            'reference_no' => 'INV-WEB-001',
            'quantity' => 1,
            'total_amount' => 500000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'ticket_code' => 'GTX-WEB-001',
            'wristband_qr' => null,
            'status' => 'sold',
        ]);

        $response = $this->actingAs($this->gateUser)
            ->withSession([
                'gate_event_id' => $this->redeemEvent->id,
                'gate_name' => 'Gate 1',
            ])
            ->postJson(route('organizer.gate.process'), [
                'ticket_code' => $ticket->ticket_code,
                'event_id' => $this->redeemEvent->id,
                'mode' => 'IN',
                'gate_name' => 'Gate 1',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'customer' => '-',
            'reference_no' => '-',
            'ticket_code' => 'GTX-WEB-001',
        ]);
        $this->assertStringNotContainsString('Doni Web', $response->getContent());
        $this->assertEmpty($response->json('custom_fields'));

        $ticket->refresh();
        $this->assertEquals('redeemed', $ticket->status);
        $this->assertNotNull($ticket->redeemed_at);
        $this->assertEquals('GTX-WEB-001', $ticket->wristband_qr);
    }

    public function test_organizer_gate_process_web_accepts_wristband_qr_and_omits_buyer_data(): void
    {
        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'customer_name' => 'Doni Web Pembeli',
            'customer_email' => 'doni@example.com',
            'customer_phone' => '08123456789',
            'reference_no' => 'INV-WEB-002',
            'quantity' => 1,
            'total_amount' => 500000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->redeemEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $this->redeemCategory->id,
            'ticket_code' => 'GTX-WEB-002',
            'wristband_qr' => 'WB-WEB-0022',
            'status' => 'redeemed',
            'redeemed_at' => now(),
        ]);

        $response = $this->actingAs($this->gateUser)
            ->withSession([
                'gate_event_id' => $this->redeemEvent->id,
                'gate_name' => 'Gate 1',
            ])
            ->postJson(route('organizer.gate.process'), [
                'ticket_code' => 'WB-WEB-0022',
                'event_id' => $this->redeemEvent->id,
                'mode' => 'IN',
                'gate_name' => 'Gate 1',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'customer' => '-',
            'reference_no' => '-',
            'ticket_code' => 'WB-WEB-0022',
        ]);
        $this->assertEmpty($response->json('custom_fields'));
    }
}
