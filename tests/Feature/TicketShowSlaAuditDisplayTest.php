<?php

namespace Tests\Feature;

use App\Models\Helpdesk\SlaPolicy;
use App\Models\Helpdesk\Ticket;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketShowSlaAuditDisplayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function audit_trail_displays_sla_policy_name_instead_of_raw_id(): void
    {
        $this->withoutMiddleware();
        // Act as ITSS so we can access the ITSS TicketShow page
        $agent = User::factory()->create();
        // Fake being in an allowed role by bypassing middleware in tests; route group still requires auth
        $this->be($agent instanceof AuthenticatableContract ? $agent : User::find($agent->id));

        // Create a policy and a ticket with that policy assigned
        $policy = SlaPolicy::create([
            'name' => 'Critical Incidents',
            'type' => 'incident',
            'priority' => 'high',
            'respond_mins' => 30,
            'resolve_mins' => 240,
            'is_active' => true,
        ]);

        $t = Ticket::factory()->create([
            'requester_id' => $agent->id,
            'sla_policy_id' => null,
        ]);

        // Trigger an update that sets the SLA policy ID to create an audit entry
        $t->update(['sla_policy_id' => $policy->id]);

        // Visit the ITSS ticket show page
        $resp = $this->get(route('itss.ticket.show', ['ticket' => $t->id]));
        $resp->assertStatus(200);

        // Assert that the policy name shows up (not just raw numeric)
        $resp->assertSee('SLA Policy Id');
        $resp->assertSee('Critical Incidents');
    }
}
