<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Gate;
use App\Models\Tenant;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\WristbandTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrganizerBulkDuplicateEventTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'Penyedia Event']);
    }

    public function test_bulk_duplicate_creates_multiple_events_with_wristbands(): void
    {
        $tenant = Tenant::create([
            'name' => 'Lampung Youth Organizer',
            'slug' => 'lampung-youth',
            'email' => 'admin@lampungyouth.com',
            'status' => 'active',
        ]);

        $user = User::create([
            'name' => 'Organizer User',
            'email' => 'organizer@lampungyouth.com',
            'password' => bcrypt('password'),
            'tenant_id' => $tenant->id,
            'is_active' => true,
        ]);
        $user->assignRole('Penyedia Event');

        $sourceEvent = Event::create([
            'tenant_id' => $tenant->id,
            'name' => 'Festival Musik Lampung Youth',
            'slug' => 'festival-musik-lampung-youth',
            'venue' => 'GSG Unila',
            'city' => 'Bandar Lampung',
            'event_start_date' => now()->addDays(2)->setHour(10)->setMinute(0),
            'event_end_date' => now()->addDays(2)->setHour(12)->setMinute(0),
            'status' => 'published',
            'is_free' => false,
        ]);

        $category = TicketCategory::create([
            'event_id' => $sourceEvent->id,
            'tenant_id' => $tenant->id,
            'name' => 'VIP Gold',
            'price' => 150000,
            'quota' => 500,
            'sold_count' => 50,
            'is_active' => true,
        ]);

        $gate = Gate::create([
            'event_id' => $sourceEvent->id,
            'tenant_id' => $tenant->id,
            'name' => 'Pintu Barat Utama',
            'is_active' => true,
        ]);
        $gate->ticketCategories()->sync([$category->id]);

        // Create Custom Event Wristband Template
        $eventWristband = WristbandTemplate::create([
            'tenant_id' => $tenant->id,
            'event_id' => $sourceEvent->id,
            'ticket_category_id' => null,
            'name' => 'Gelang Festival Custom',
            'mode' => 'custom',
            'columns_config' => [
                'event_name' => ['enabled' => true, 'font_size' => '10pt'],
            ],
        ]);

        // Create Category Wristband Template
        $catWristband = WristbandTemplate::create([
            'tenant_id' => $tenant->id,
            'event_id' => $sourceEvent->id,
            'ticket_category_id' => $category->id,
            'name' => 'Gelang VIP Only',
            'mode' => 'custom',
            'columns_config' => [
                'category_name' => ['enabled' => true, 'color' => '#FFD700'],
            ],
        ]);

        $response = $this->actingAs($user)->post(route('organizer.events.bulk-duplicate'), [
            'source_event_id' => $sourceEvent->id,
            'copies_count' => 15,
            'naming_pattern' => 'session',
            'status' => 'draft',
            'date_offset_mode' => 'hours',
            'date_offset_value' => 2,
        ]);

        $response->assertRedirect(route('organizer.events.index'));
        $response->assertSessionHas('success');

        // Verify that 15 new events were created (Total 16 events)
        $this->assertEquals(16, Event::where('tenant_id', $tenant->id)->count());

        // Check created sessions
        $duplicatedEvents = Event::where('tenant_id', $tenant->id)
            ->where('id', '!=', $sourceEvent->id)
            ->orderBy('id', 'asc')
            ->get();

        $this->assertCount(15, $duplicatedEvents);

        foreach ($duplicatedEvents as $idx => $dup) {
            $sessionNum = $idx + 1;
            $this->assertStringContainsString("Festival Musik Lampung Youth - Sesi {$sessionNum}", $dup->name);
            $this->assertEquals('draft', $dup->status);
            $this->assertNotEmpty($dup->security_code);
            $this->assertEquals(6, strlen($dup->security_code));

            // Verify ticket category replication & reset sold_count
            $dupCategories = TicketCategory::where('event_id', $dup->id)->get();
            $this->assertCount(1, $dupCategories);
            $dupCat = $dupCategories->first();
            $this->assertEquals('VIP Gold', $dupCat->name);
            $this->assertEquals(150000, $dupCat->price);
            $this->assertEquals(500, $dupCat->quota);
            $this->assertEquals(0, $dupCat->sold_count);

            // Verify gates replication and category linkage
            $dupGates = Gate::where('event_id', $dup->id)->with('ticketCategories')->get();
            $this->assertCount(1, $dupGates);
            $dupGate = $dupGates->first();
            $this->assertEquals('Pintu Barat Utama', $dupGate->name);
            $this->assertEquals([$dupCat->id], $dupGate->ticketCategories->pluck('id')->toArray());

            // Verify Event-Level Wristband Template replication
            $dupEventWristband = WristbandTemplate::where('event_id', $dup->id)
                ->whereNull('ticket_category_id')
                ->first();
            $this->assertNotNull($dupEventWristband);
            $this->assertEquals('custom', $dupEventWristband->mode);
            $this->assertEquals('Gelang Festival Custom', $dupEventWristband->name);

            // Verify Category-Level Wristband Template replication
            $dupCatWristband = WristbandTemplate::where('event_id', $dup->id)
                ->where('ticket_category_id', $dupCat->id)
                ->first();
            $this->assertNotNull($dupCatWristband);
            $this->assertEquals('custom', $dupCatWristband->mode);
            $this->assertEquals('Gelang VIP Only', $dupCatWristband->name);
        }
    }
}
