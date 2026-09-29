<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Tenant;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantPublicEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_event_listing_page_loads_successfully(): void
    {
        $tenant = Tenant::create([
            'name' => 'Lampung Youth Community',
            'slug' => 'lampung-youth',
            'email' => 'info@lampungyouth.com',
            'status' => 'active',
        ]);

        $event1 = Event::create([
            'tenant_id' => $tenant->id,
            'name' => 'Konser Musik Lampung Youth Fest',
            'slug' => 'konser-lampung-youth-fest',
            'venue' => 'GSG Unila',
            'city' => 'Bandar Lampung',
            'event_start_date' => now()->addDays(5),
            'event_end_date' => now()->addDays(5)->addHours(4),
            'status' => 'published',
            'is_free' => false,
        ]);

        TicketCategory::create([
            'event_id' => $event1->id,
            'tenant_id' => $tenant->id,
            'name' => 'Presale 1',
            'price' => 50000,
            'quota' => 100,
            'sold_count' => 0,
            'is_active' => true,
        ]);

        // Other tenant event that should NOT appear
        $otherTenant = Tenant::create([
            'name' => 'Other Organizer',
            'slug' => 'other-organizer',
            'email' => 'other@organizer.com',
            'status' => 'active',
        ]);

        Event::create([
            'tenant_id' => $otherTenant->id,
            'name' => 'Event Dari Tenant Lain',
            'slug' => 'event-dari-tenant-lain',
            'venue' => 'Jakarta Expo',
            'city' => 'Jakarta',
            'event_start_date' => now()->addDays(10),
            'event_end_date' => now()->addDays(10)->addHours(4),
            'status' => 'published',
            'is_free' => false,
        ]);

        $response = $this->get('/lampung-youth/list-event');

        $response->assertStatus(200);
        $response->assertSee('Lampung Youth Community');
        $response->assertSee('Konser Musik Lampung Youth Fest');
        $response->assertSee('Bandar Lampung');
        $response->assertSee('50.000');
        $response->assertDontSee('Event Dari Tenant Lain');
    }

    public function test_tenant_events_search_and_filter(): void
    {
        $tenant = Tenant::create([
            'name' => 'Lampung Youth',
            'slug' => 'lampung-youth',
            'email' => 'contact@lampungyouth.com',
            'status' => 'active',
        ]);

        Event::create([
            'tenant_id' => $tenant->id,
            'name' => 'Festival Kopi Lampung',
            'slug' => 'festival-kopi-lampung',
            'venue' => 'Lapangan Saburai',
            'city' => 'Bandar Lampung',
            'event_start_date' => now()->addDays(3),
            'event_end_date' => now()->addDays(3)->addHours(5),
            'status' => 'published',
            'is_free' => false,
        ]);

        Event::create([
            'tenant_id' => $tenant->id,
            'name' => 'Turnamen E-Sport Lampung',
            'slug' => 'turnamen-esport-lampung',
            'venue' => 'Mall Boemi Kedaton',
            'city' => 'Bandar Lampung',
            'event_start_date' => now()->addDays(7),
            'event_end_date' => now()->addDays(7)->addHours(6),
            'status' => 'published',
            'is_free' => false,
        ]);

        // Search for 'Kopi'
        $response = $this->get('/lampung-youth/list-event?q=Kopi');
        $response->assertStatus(200);
        $response->assertSee('Festival Kopi Lampung');
        $response->assertDontSee('Turnamen E-Sport Lampung');

        // Search for 'Sport'
        $response2 = $this->get('/lampung-youth/list-event?q=Sport');
        $response2->assertStatus(200);
        $response2->assertSee('Turnamen E-Sport Lampung');
        $response2->assertDontSee('Festival Kopi Lampung');
    }

    public function test_non_existent_tenant_returns_404(): void
    {
        $response = $this->get('/non-existent-tenant-slug/list-event');
        $response->assertStatus(404);
    }

    public function test_organizer_events_route_is_not_hijacked_by_tenant_route(): void
    {
        // Unauthenticated request to /organizer/events should redirect to login, not 404
        $response = $this->get('/organizer/events');
        $response->assertRedirect('/login');
    }
}
