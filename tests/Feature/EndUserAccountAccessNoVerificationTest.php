<?php

namespace Tests\Feature;

use App\Livewire\Tickets\MyTickets;
use App\Models\Helpdesk\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EndUserAccountAccessNoVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_end_user_can_create_account_access_ticket_without_verification(): void
    {
        /** @var User $user */
        $user = User::factory()->createOne();
        $category = TicketCategory::firstOrCreate(
            ['name' => 'Account Access'],
            ['description' => 'Account access requests', 'is_active' => true]
        );

        $this->actingAs($user);

        Livewire::test(MyTickets::class)
            ->set('showCreate', true)
            ->set('subject', 'Need access to portal')
            ->set('description', 'Please grant access')
            ->set('category_id', $category->id)
            ->set('priority_new', 'medium')
            // Do NOT set any verification fields
            ->call('createTicket')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Need access to portal',
            'verification_status' => 'verified', // should auto-verify for end-users
            'category_id' => $category->id,
            'requester_id' => $user->id,
        ]);
    }
}
