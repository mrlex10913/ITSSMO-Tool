<?php

namespace Tests\Feature;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketAuditLog;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAuditLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_logs_creation_and_update(): void
    {
        $user = User::factory()->create();
        $this->be($user instanceof AuthenticatableContract ? $user : User::find($user->id));

        // Create
        $t = Ticket::factory()->create([
            'status' => 'open',
            'priority' => 'medium',
            'requester_id' => $user->id,
        ]);

        $this->assertDatabaseHas('ticket_audit_logs', [
            'ticket_id' => $t->id,
            'event' => 'created',
        ]);

        // Update
        $t->update(['status' => 'in_progress']);

        $this->assertDatabaseHas('ticket_audit_logs', [
            'ticket_id' => $t->id,
            'event' => 'updated',
        ]);

        $log = TicketAuditLog::where('ticket_id', $t->id)->where('event', 'updated')->latest()->first();
        $this->assertNotNull($log);
        $this->assertArrayHasKey('status', $log->changes);
        $this->assertEquals('open', $log->changes['status']['from']);
        $this->assertEquals('in_progress', $log->changes['status']['to']);
    }
}
