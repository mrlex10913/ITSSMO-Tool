<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckTemporaryPassword;
use App\Models\Helpdesk\AssignmentRule;
use App\Models\Helpdesk\CannedResponse;
use App\Models\Helpdesk\SlaEscalation;
use App\Models\Helpdesk\SlaPolicy;
use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketMacro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;
use Tests\TestCase;

class HelpdeskPhase2ATest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_view_ticket_show_and_see_canned_and_macros(): void
    {
        /** @var User $agent */
        $agent = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($agent, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);
        $t = Ticket::factory()->create(['subject' => 'Test', 'priority' => 'medium', 'type' => 'incident']);
        CannedResponse::factory()->create(['title' => 'Hello', 'body' => 'Hello there']);
        TicketMacro::factory()->create(['name' => 'Resolve', 'actions' => ['status' => 'resolved']]);

        $resp = $this->get(route('itss.ticket.show', ['ticket' => $t->id]));
        $resp->assertStatus(200);
        $resp->assertSee('Canned response');
        $resp->assertSee('Macros');
    }

    public function test_admin_can_create_assignment_rule_and_list_it(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($admin, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);

        // Create a rule model directly
        $rule = AssignmentRule::create([
            'name' => 'Incidents to Admin',
            'criteria' => ['type' => 'incident', 'priority' => 'medium', 'category_id' => null, 'keywords' => ''],
            'assignee_id' => $admin->id,
            'is_active' => true,
        ]);

        $resp = $this->get(route('itss.assignment-rules'));
        $resp->assertStatus(200)->assertSee('Assignment Rules')->assertSee('Incidents to Admin');
    }

    public function test_admin_can_view_sla_escalations_page_and_add_rule(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($admin, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);

        $policy = SlaPolicy::create([
            'name' => 'Inc Med',
            'type' => 'incident',
            'priority' => 'medium',
            'respond_mins' => 30,
            'resolve_mins' => 240,
            'is_active' => true,
        ]);

        SlaEscalation::create([
            'sla_policy_id' => $policy->id,
            'threshold_mins_before_breach' => 30,
            'escalate_to_user_id' => $admin->id,
            'is_active' => true,
        ]);

        $resp = $this->get(route('itss.sla.escalations', ['policy' => $policy->id]));
        $resp->assertStatus(200)->assertSee('SLA Escalations')->assertSee('30 minutes before breach');
    }

    public function test_macro_application_updates_ticket_and_optionally_sets_reply(): void
    {
        /** @var User $agent */
        $agent = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($agent, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);

        $t = Ticket::factory()->create([
            'requester_id' => $agent->id, // ensure ownership for non-itss route context in Livewire tests
            'status' => 'open',
            'type' => 'incident',
            'priority' => 'medium',
        ]);

        $macro = TicketMacro::create([
            'name' => 'Start Progress',
            'actions' => [
                'status' => 'in_progress',
                'reply' => 'We are now looking into this.',
            ],
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\ITSS\TicketShow::class, ['ticket' => $t->id])
            ->call('applyMacro', $macro->id)
            ->tap(function ($lw) use ($t) {
                $this->assertEquals('in_progress', \App\Models\Helpdesk\Ticket::find($t->id)->status);
                $this->assertEquals('We are now looking into this.', $lw->get('commentBody'));
            });

        $this->assertDatabaseHas('tickets', [
            'id' => $t->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_canned_response_insertion_and_comment_creation(): void
    {
        /** @var User $agent */
        $agent = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($agent, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);

        $t = Ticket::factory()->create([
            'requester_id' => $agent->id,
            'status' => 'open',
        ]);
        $cr = CannedResponse::factory()->create(['body' => 'Standard canned reply']);

        Livewire::test(\App\Livewire\ITSS\TicketShow::class, ['ticket' => $t->id])
            ->set('commentBody', $cr->body)
            ->set('isInternal', false)
            ->call('addComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ticket_comments', [
            'ticket_id' => $t->id,
            'body' => 'Standard canned reply',
            'is_internal' => 0,
        ]);
    }

    public function test_helpdesk_list_shows_escalation_indicator_when_within_threshold(): void
    {
        /** @var User $agent */
        $agent = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($agent, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);

        $policy = SlaPolicy::create([
            'name' => 'Incident Medium',
            'type' => 'incident',
            'priority' => 'medium',
            'respond_mins' => 30,
            'resolve_mins' => 240,
            'is_active' => true,
        ]);

        SlaEscalation::create([
            'sla_policy_id' => $policy->id,
            'threshold_mins_before_breach' => 30,
            'escalate_to_user_id' => $agent->id,
            'is_active' => true,
        ]);

        $t = Ticket::factory()->create([
            'status' => 'open',
            'type' => 'incident',
            'priority' => 'medium',
            'sla_policy_id' => $policy->id,
            'sla_due_at' => now()->addMinutes(20), // within 30-minute threshold
        ]);

        $resp = $this->get(route('itss.helpdesk'));
        $resp->assertStatus(200)->assertSee('Esc in');
    }

    public function test_my_queue_filter_limits_tickets_to_assigned_agent(): void
    {
        /** @var User $agent */
        $agent = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($agent, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);

        $mine = Ticket::factory()->create(['assignee_id' => $agent->id, 'subject' => 'Mine Ticket']);
        $other = Ticket::factory()->create(['assignee_id' => null, 'subject' => 'Other Ticket']);

        $resp = $this->get(route('itss.helpdesk', ['mine' => '1']));
        $resp->assertStatus(200);
        $resp->assertSee('Mine Ticket');
        $resp->assertDontSee('Other Ticket');
    }

    public function test_unassigned_filter_limits_tickets_to_those_without_assignee(): void
    {
        /** @var User $agent */
        $agent = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($agent, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);

        $un = Ticket::factory()->create(['assignee_id' => null, 'subject' => 'Unassigned Ticket']);
        $as = Ticket::factory()->create(['assignee_id' => $agent->id, 'subject' => 'Assigned Ticket']);

        $resp = $this->get(route('itss.helpdesk', ['unassigned' => '1']));
        $resp->assertStatus(200);
        $resp->assertSee('Unassigned Ticket');
        $resp->assertDontSee('Assigned Ticket');
    }

    public function test_assignee_filter_limits_tickets_to_selected_user(): void
    {
        /** @var User $agent */
        $agent = User::factory()->create(['email_verified_at' => now()]);
        $other = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($agent, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);

        Ticket::factory()->create(['assignee_id' => $agent->id, 'subject' => 'Agent Ticket']);
        Ticket::factory()->create(['assignee_id' => $other->id, 'subject' => 'Other Agent Ticket']);

        $resp = $this->get(route('itss.helpdesk', ['assignee' => $agent->id]));
        $resp->assertStatus(200);
        $resp->assertSee('Agent Ticket');
        $resp->assertDontSee('Other Agent Ticket');
    }

    public function test_filters_persist_across_requests_via_session(): void
    {
        /** @var User $agent */
        $agent = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($agent, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);

        // First request sets filters
        $this->get(route('itss.helpdesk', ['status' => 'open', 'priority' => 'high', 'mine' => '1']))->assertStatus(200);
        // Second request without query parameters should keep prior filters active
        $resp = $this->get(route('itss.helpdesk'));
        $resp->assertStatus(200);
        // Quick heuristic: chips render when filters present
        $resp->assertSee('My queue');
        $resp->assertSee('Priority: High');
    }

    public function test_escalations_only_filter_shows_only_near_breach_tickets(): void
    {
        /** @var User $agent */
        $agent = User::factory()->create(['email_verified_at' => now()]);
        Sanctum::actingAs($agent, ['*']);
        $this->withoutMiddleware([CheckRole::class, CheckTemporaryPassword::class]);

        $policy = \App\Models\Helpdesk\SlaPolicy::create([
            'name' => 'Inc Med', 'type' => 'incident', 'priority' => 'medium', 'respond_mins' => 30, 'resolve_mins' => 240, 'is_active' => true,
        ]);
        \App\Models\Helpdesk\SlaEscalation::create([
            'sla_policy_id' => $policy->id,
            'threshold_mins_before_breach' => 30,
            'escalate_to_user_id' => $agent->id,
            'is_active' => true,
        ]);

        $near = \App\Models\Helpdesk\Ticket::factory()->create([
            'subject' => 'Near breach',
            'status' => 'open',
            'type' => 'incident',
            'priority' => 'medium',
            'sla_policy_id' => $policy->id,
            'sla_due_at' => now()->addMinutes(10),
        ]);
        $far = \App\Models\Helpdesk\Ticket::factory()->create([
            'subject' => 'Far from breach',
            'status' => 'open',
            'type' => 'incident',
            'priority' => 'medium',
            'sla_policy_id' => $policy->id,
            'sla_due_at' => now()->addHours(10),
        ]);

        $resp = $this->get(route('itss.helpdesk', ['escalationsOnly' => '1']));
        $resp->assertStatus(200);
        $resp->assertSee('Near breach');
        $resp->assertDontSee('Far from breach');
    }
}
