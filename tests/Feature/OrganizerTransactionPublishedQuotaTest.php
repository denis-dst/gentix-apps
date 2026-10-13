<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrganizerTransactionPublishedQuotaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Tenant $tenant;
    protected Event $publishedEvent;
    protected Event $draftEvent;
    protected TicketCategory $publishedCategory;
    protected TicketCategory $draftCategory;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Penyedia Event']);

        $this->tenant = Tenant::create([
            'name' => 'Konser Nusantara',
            'slug' => 'konser-nusantara',
            'email' => 'admin@konsernusantara.com',
            'status' => 'active',
        ]);

        $this->user = User::create([
            'name' => 'Organizer Lead',
            'email' => 'lead@konsernusantara.com',
            'password' => bcrypt('password123'),
            'tenant_id' => $this->tenant->id,
            'is_active' => true,
        ]);
        $this->user->assignRole('Penyedia Event');

        // 1. Published Event with 100 quota
        $this->publishedEvent = Event::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Published Rock Fest',
            'slug' => 'published-rock-fest',
            'venue' => 'Gelora Bung Karno',
            'city' => 'Jakarta',
            'event_start_date' => now()->addDays(10),
            'event_end_date' => now()->addDays(11),
            'status' => 'published',
            'is_free' => false,
        ]);

        $this->publishedCategory = TicketCategory::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->publishedEvent->id,
            'name' => 'VIP Published',
            'price' => 200000,
            'quota' => 100,
            'sold_count' => 10,
            'is_active' => true,
        ]);

        // 2. Draft Event with 500 quota (should NOT be included in summary quota)
        $this->draftEvent = Event::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Draft Jazz Night',
            'slug' => 'draft-jazz-night',
            'venue' => 'Balai Sarbini',
            'city' => 'Jakarta',
            'event_start_date' => now()->addDays(20),
            'event_end_date' => now()->addDays(21),
            'status' => 'draft',
            'is_free' => false,
        ]);

        $this->draftCategory = TicketCategory::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->draftEvent->id,
            'name' => 'Regular Draft',
            'price' => 100000,
            'quota' => 500,
            'sold_count' => 0,
            'is_active' => true,
        ]);
    }

    public function test_transactions_summary_quota_only_includes_published_events(): void
    {
        $response = $this->actingAs($this->user)->get(route('organizer.transactions.index'));

        $response->assertStatus(200);

        // Quota summary must be 100 (from published event only), NOT 600 (which would include the draft)
        $this->assertEquals(100, $response->viewData('totalQuota'));

        // Category stats should only contain categories from published events
        $categoryStats = $response->viewData('categoryStats');
        $this->assertCount(1, $categoryStats);
        $this->assertEquals('VIP Published', $categoryStats->first()->name);
        $this->assertEquals(100, $categoryStats->first()->quota);

        // Filter dropdown ticketCategories should also only contain published categories
        $ticketCategories = $response->viewData('ticketCategories');
        $this->assertCount(1, $ticketCategories);
        $this->assertEquals('VIP Published', $ticketCategories->first()->name);
    }

    public function test_transactions_summary_quota_filters_by_specific_published_event(): void
    {
        // Filter by the published event ID
        $response = $this->actingAs($this->user)->get(route('organizer.transactions.index', [
            'event_id' => $this->publishedEvent->id,
        ]));

        $response->assertStatus(200);
        $this->assertEquals(100, $response->viewData('totalQuota'));
        $categoryStats = $response->viewData('categoryStats');
        $this->assertCount(1, $categoryStats);
        $this->assertEquals('VIP Published', $categoryStats->first()->name);
    }

    public function test_transactions_summary_quota_returns_zero_for_draft_event(): void
    {
        // When someone explicitly passes a draft event ID, published quota is 0
        $response = $this->actingAs($this->user)->get(route('organizer.transactions.index', [
            'event_id' => $this->draftEvent->id,
        ]));

        $response->assertStatus(200);
        $this->assertEquals(0, $response->viewData('totalQuota'));
        $categoryStats = $response->viewData('categoryStats');
        $this->assertCount(0, $categoryStats);
    }

    public function test_transactions_summary_never_displays_negative_sold_count_when_tickets_deleted(): void
    {
        // Create a published category with no active tickets and manually set raw negative sold_count
        $emptyCategory = TicketCategory::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->publishedEvent->id,
            'name' => 'Premenly Round Day 2',
            'price' => 15000,
            'quota' => 500,
            'sold_count' => 0,
            'is_active' => true,
        ]);
        // Simulate historical raw database decrement that made it negative
        \DB::table('ticket_categories')->where('id', $emptyCategory->id)->update(['sold_count' => -2]);

        $response = $this->actingAs($this->user)->get(route('organizer.transactions.index'));

        $response->assertStatus(200);

        $categoryStats = $response->viewData('categoryStats');
        $cat2 = $categoryStats->firstWhere('id', $emptyCategory->id);

        $this->assertNotNull($cat2);
        // Sold must be 0, never -2!
        $this->assertEquals(0, $cat2->sold);
        // Unsold must be 500, never 502!
        $this->assertEquals(500, $cat2->unsold);

        // Top-level totalSold and totalUnsold must be fully synchronized
        $this->assertEquals($categoryStats->sum('sold'), $response->viewData('totalSold'));
        $this->assertEquals($categoryStats->sum('unsold'), $response->viewData('totalUnsold'));
    }

    public function test_cancelling_ticket_does_not_decrement_sold_count_below_zero(): void
    {
        $category = TicketCategory::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->publishedEvent->id,
            'name' => 'Single Ticket Category',
            'price' => 50000,
            'quota' => 10,
            'sold_count' => 0, // already 0
            'is_active' => true,
        ]);

        $tx = Transaction::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->publishedEvent->id,
            'ticket_category_id' => $category->id,
            'reference_no' => 'INV-TEST-CANCEL-001',
            'customer_name' => 'Cancel Test',
            'customer_email' => 'cancel@test.com',
            'customer_phone' => '08123456789',
            'quantity' => 1,
            'total_amount' => 50000,
            'payment_status' => 'paid',
            'channel' => 'online',
        ]);

        $ticket = Ticket::create([
            'tenant_id' => $this->tenant->id,
            'event_id' => $this->publishedEvent->id,
            'transaction_id' => $tx->id,
            'ticket_category_id' => $category->id,
            'ticket_code' => 'GTX-CANCEL-001',
            'status' => 'sold',
        ]);

        // Cancel this ticket via organizer
        $response = $this->actingAs($this->user)->post(route('organizer.tickets.cancel', $ticket));
        $response->assertSessionHas('success');

        $category->refresh();
        $this->assertGreaterThanOrEqual(0, $category->sold_count);
        $this->assertEquals(0, $category->sold_count);
    }
}
