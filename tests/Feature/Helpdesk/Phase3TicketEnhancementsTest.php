<?php

namespace Tests\Feature\Helpdesk;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketLink;
use App\Models\Helpdesk\TicketTag;
use App\Models\Helpdesk\TicketTimeEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase3TicketEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    // ===== TICKET TAGS =====

    public function test_ticket_tag_can_be_created(): void
    {
        $user = User::factory()->create();

        $tag = TicketTag::create([
            'name' => 'Urgent',
            'color' => '#ef4444',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('ticket_tags', [
            'name' => 'Urgent',
            'slug' => 'urgent',
            'color' => '#ef4444',
        ]);
    }

    public function test_ticket_tag_slug_auto_generated(): void
    {
        $tag = TicketTag::create([
            'name' => 'High Priority',
            'color' => '#f97316',
        ]);

        $this->assertEquals('high-priority', $tag->slug);
    }

    public function test_ticket_can_have_multiple_tags(): void
    {
        $ticket = Ticket::factory()->create();
        $tag1 = TicketTag::create(['name' => 'Bug', 'color' => '#ef4444']);
        $tag2 = TicketTag::create(['name' => 'Feature', 'color' => '#22c55e']);

        $ticket->tags()->attach([$tag1->id, $tag2->id]);

        $this->assertCount(2, $ticket->fresh()->tags);
        $this->assertTrue($ticket->tags->contains($tag1));
        $this->assertTrue($ticket->tags->contains($tag2));
    }

    public function test_tag_scope_active_works(): void
    {
        TicketTag::create(['name' => 'Active Tag', 'color' => '#000', 'is_active' => true]);
        TicketTag::create(['name' => 'Inactive Tag', 'color' => '#000', 'is_active' => false]);

        $active = TicketTag::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals('Active Tag', $active->first()->name);
    }

    // ===== TIME TRACKING =====

    public function test_time_entry_can_be_logged(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();

        $entry = TicketTimeEntry::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'duration_mins' => 45,
            'description' => 'Debugging issue',
            'work_date' => now(),
            'is_billable' => true,
        ]);

        $this->assertDatabaseHas('ticket_time_entries', [
            'ticket_id' => $ticket->id,
            'duration_mins' => 45,
            'is_billable' => true,
        ]);
    }

    public function test_time_entry_formatted_duration(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();

        $entry = TicketTimeEntry::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'duration_mins' => 90,
            'work_date' => now(),
        ]);

        $this->assertEquals('1h 30m', $entry->formatted_duration);
    }

    public function test_time_entry_hours_only(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();

        $entry = TicketTimeEntry::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'duration_mins' => 120,
            'work_date' => now(),
        ]);

        $this->assertEquals('2h', $entry->formatted_duration);
    }

    public function test_time_entry_minutes_only(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();

        $entry = TicketTimeEntry::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'duration_mins' => 25,
            'work_date' => now(),
        ]);

        $this->assertEquals('25m', $entry->formatted_duration);
    }

    public function test_ticket_total_time_minutes(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();

        TicketTimeEntry::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'duration_mins' => 30,
            'work_date' => now(),
        ]);

        TicketTimeEntry::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'duration_mins' => 45,
            'work_date' => now(),
        ]);

        $this->assertEquals(75, $ticket->fresh()->total_time_minutes);
    }

    public function test_ticket_formatted_total_time(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();

        TicketTimeEntry::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'duration_mins' => 90,
            'work_date' => now(),
        ]);

        $this->assertEquals('1h 30m', $ticket->fresh()->formatted_total_time);
    }

    // ===== TICKET LINKING =====

    public function test_ticket_link_can_be_created(): void
    {
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();
        $user = User::factory()->create();

        $link = TicketLink::create([
            'ticket_id' => $ticket1->id,
            'linked_ticket_id' => $ticket2->id,
            'link_type' => 'related',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('ticket_links', [
            'ticket_id' => $ticket1->id,
            'linked_ticket_id' => $ticket2->id,
            'link_type' => 'related',
        ]);
    }

    public function test_link_type_label(): void
    {
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();

        $link = TicketLink::create([
            'ticket_id' => $ticket1->id,
            'linked_ticket_id' => $ticket2->id,
            'link_type' => 'parent',
        ]);

        $this->assertEquals('Parent of', $link->link_type_label);
    }

    public function test_inverse_link_types(): void
    {
        $this->assertEquals('child', TicketLink::getInverseLinkType('parent'));
        $this->assertEquals('parent', TicketLink::getInverseLinkType('child'));
        $this->assertEquals('blocked_by', TicketLink::getInverseLinkType('blocks'));
        $this->assertEquals('blocks', TicketLink::getInverseLinkType('blocked_by'));
        $this->assertEquals('related', TicketLink::getInverseLinkType('related'));
        $this->assertEquals('duplicate', TicketLink::getInverseLinkType('duplicate'));
    }

    public function test_ticket_outgoing_links(): void
    {
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();

        TicketLink::create([
            'ticket_id' => $ticket1->id,
            'linked_ticket_id' => $ticket2->id,
            'link_type' => 'blocks',
        ]);

        $this->assertCount(1, $ticket1->fresh()->outgoingLinks);
        $this->assertEquals($ticket2->id, $ticket1->outgoingLinks->first()->linked_ticket_id);
    }

    public function test_ticket_incoming_links(): void
    {
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();

        TicketLink::create([
            'ticket_id' => $ticket1->id,
            'linked_ticket_id' => $ticket2->id,
            'link_type' => 'blocks',
        ]);

        $this->assertCount(1, $ticket2->fresh()->incomingLinks);
        $this->assertEquals($ticket1->id, $ticket2->incomingLinks->first()->ticket_id);
    }

    public function test_linked_tickets_attribute_merges_both_directions(): void
    {
        $ticket1 = Ticket::factory()->create();
        $ticket2 = Ticket::factory()->create();
        $ticket3 = Ticket::factory()->create();

        // ticket1 blocks ticket2
        TicketLink::create([
            'ticket_id' => $ticket1->id,
            'linked_ticket_id' => $ticket2->id,
            'link_type' => 'blocks',
        ]);

        // ticket3 is parent of ticket1
        TicketLink::create([
            'ticket_id' => $ticket3->id,
            'linked_ticket_id' => $ticket1->id,
            'link_type' => 'parent',
        ]);

        $linked = $ticket1->fresh()->linked_tickets;

        $this->assertCount(2, $linked);
    }
}
