<?php

namespace Tests\Feature;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketCategory;
use App\Models\Helpdesk\TicketComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_logs_are_recorded_for_create_comment_and_update(): void
    {
        $this->withoutMiddleware();
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        /** @var \App\Models\User $agent */
        $agent = User::factory()->create();
        $cat = TicketCategory::factory()->create();

        $this->actingAs($user);
        $t = Ticket::create([
            'ticket_no' => 'HD-2025-00001',
            'subject' => 'Printer issue',
            'description' => 'Cannot print',
            'status' => 'open',
            'priority' => 'medium',
            'category_id' => $cat->id,
            'requester_id' => $user->id,
        ]);

        $this->assertDatabaseHas('ticket_activity_logs', [
            'ticket_id' => $t->id,
            'action' => 'created',
        ]);

        $this->actingAs($agent);
        TicketComment::create([
            'ticket_id' => $t->id,
            'user_id' => $agent->id,
            'is_internal' => false,
            'body' => 'On it',
        ]);

        $this->assertDatabaseHas('ticket_activity_logs', [
            'ticket_id' => $t->id,
            'action' => 'comment_public',
        ]);

        $t->status = 'in_progress';
        $t->assignee_id = $agent->id;
        $t->save();

        $this->assertDatabaseHas('ticket_activity_logs', [
            'ticket_id' => $t->id,
            'action' => 'updated_status',
        ]);
        $this->assertDatabaseHas('ticket_activity_logs', [
            'ticket_id' => $t->id,
            'action' => 'updated_assignee',
        ]);
    }
}
