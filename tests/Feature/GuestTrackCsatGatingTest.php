<?php

namespace Tests\Feature;

use App\Livewire\Tickets\GuestTrack;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketCategory;
use App\Models\Helpdesk\TicketComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuestTrackCsatGatingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_must_submit_survey_before_agent_replies_are_visible(): void
    {
        $category = TicketCategory::factory()->create(['name' => 'General']);
        $requester = User::factory()->create(['email' => 'guest@example.com']);
        $ticket = Ticket::factory()->create([
            'category_id' => $category->id,
            'requester_id' => $requester->id,
        ]);

        // Agent public reply
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => null,
            'is_internal' => false,
            'body' => 'Agent reply body',
        ]);

        $component = Livewire::test(GuestTrack::class)
            ->set('ticket_no', $ticket->ticket_no)
            ->set('email', 'guest@example.com')
            ->call('lookup')
            ->assertSet('requireCsatToView', true)
            ->assertDontSee('Agent reply body');

        // Complete CSAT
        $component
            ->set('csatRating', 5)
            ->set('csatComment', 'Great!')
            ->call('submitCsat')
            ->assertHasNoErrors()
            ->assertSet('requireCsatToView', false)
            ->assertSee('Agent reply body');

        $this->assertDatabaseHas('csat_responses', [
            'ticket_id' => $ticket->id,
            'rating' => 'good',
        ]);
    }
}
