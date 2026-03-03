<?php

namespace Tests\Feature\Helpdesk;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketTimeEntry;
use App\Models\Roles;
use App\Models\User;
use App\Services\Helpdesk\HelpdeskReportingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase4ReportingTest extends TestCase
{
    use RefreshDatabase;

    protected HelpdeskReportingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new HelpdeskReportingService;
    }

    public function test_summary_stats_returns_correct_structure(): void
    {
        // Create some tickets
        Ticket::factory()->count(5)->create(['status' => 'open']);
        Ticket::factory()->count(3)->create([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        $stats = $this->service->getSummaryStats(30);

        $this->assertArrayHasKey('created', $stats);
        $this->assertArrayHasKey('resolved', $stats);
        $this->assertArrayHasKey('open', $stats);
        $this->assertArrayHasKey('by_status', $stats);
        $this->assertArrayHasKey('by_priority', $stats);
        $this->assertArrayHasKey('by_type', $stats);
    }

    public function test_summary_stats_counts_tickets_correctly(): void
    {
        Ticket::factory()->count(3)->create(['status' => 'open']);
        Ticket::factory()->count(2)->create(['status' => 'in_progress']);

        $stats = $this->service->getSummaryStats(30);

        $this->assertEquals(5, $stats['created']);
        $this->assertEquals(5, $stats['open']); // open + in_progress
    }

    public function test_volume_trends_returns_daily_data(): void
    {
        Ticket::factory()->create(['created_at' => now()]);
        Ticket::factory()->create(['created_at' => now()->subDays(1)]);

        $trends = $this->service->getTicketVolumeTrends(7);

        $this->assertCount(8, $trends); // 7 days + today
        $this->assertArrayHasKey('date', $trends->first());
        $this->assertArrayHasKey('label', $trends->first());
        $this->assertArrayHasKey('created', $trends->first());
        $this->assertArrayHasKey('resolved', $trends->first());
    }

    public function test_agent_performance_includes_all_metrics(): void
    {
        // Create an agent role using firstOrCreate to avoid unique constraint violations
        $role = Roles::firstOrCreate(['slug' => 'itss'], ['name' => 'ITSS']);
        $agent = User::factory()->create(['role_id' => $role->id]);

        // Create tickets assigned to agent
        $ticket = Ticket::factory()->create([
            'assignee_id' => $agent->id,
            'status' => 'resolved',
            'resolved_at' => now(),
            'responded_at' => now()->subMinutes(30),
            'sla_due_at' => now()->addHours(4),
        ]);

        // Log time
        TicketTimeEntry::create([
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'duration_mins' => 60,
            'work_date' => now(),
        ]);

        $performance = $this->service->getAgentPerformance(30);

        $this->assertCount(1, $performance);

        $agentData = $performance->first();
        $this->assertEquals($agent->id, $agentData['id']);
        $this->assertEquals($agent->name, $agentData['name']);
        $this->assertEquals(1, $agentData['assigned']);
        $this->assertEquals(1, $agentData['resolved']);
        $this->assertEquals(100.0, $agentData['resolution_rate']);
        $this->assertEquals(1.0, $agentData['time_logged_hours']);
    }

    public function test_sla_compliance_calculates_correctly(): void
    {
        // Compliant ticket (resolved before SLA)
        Ticket::factory()->create([
            'status' => 'resolved',
            'resolved_at' => now(),
            'sla_due_at' => now()->addHours(2),
        ]);

        // Breached ticket (resolved after SLA)
        Ticket::factory()->create([
            'status' => 'resolved',
            'resolved_at' => now(),
            'sla_due_at' => now()->subHours(1),
        ]);

        $sla = $this->service->getSlaCompliance(30);

        $this->assertEquals(2, $sla['total']);
        $this->assertEquals(1, $sla['compliant']);
        $this->assertEquals(1, $sla['breached']);
        $this->assertEquals(50.0, $sla['compliance_rate']);
    }

    public function test_sla_compliance_by_priority(): void
    {
        Ticket::factory()->create([
            'priority' => 'high',
            'status' => 'resolved',
            'resolved_at' => now(),
            'sla_due_at' => now()->addHours(2),
        ]);

        Ticket::factory()->create([
            'priority' => 'low',
            'status' => 'resolved',
            'resolved_at' => now(),
            'sla_due_at' => now()->subHours(1),
        ]);

        $sla = $this->service->getSlaCompliance(30);

        $byPriority = $sla['by_priority'];
        $high = $byPriority->firstWhere('priority', 'high');
        $low = $byPriority->firstWhere('priority', 'low');

        $this->assertEquals(100.0, $high['compliance_rate']);
        $this->assertEquals(0.0, $low['compliance_rate']);
    }

    public function test_top_categories_returns_sorted_list(): void
    {
        $cat1 = \App\Models\Helpdesk\TicketCategory::firstOrCreate(
            ['name' => 'Hardware'],
            ['slug' => 'hardware']
        );
        $cat2 = \App\Models\Helpdesk\TicketCategory::firstOrCreate(
            ['name' => 'Software'],
            ['slug' => 'software']
        );

        Ticket::factory()->count(5)->create(['category_id' => $cat1->id]);
        Ticket::factory()->count(3)->create(['category_id' => $cat2->id]);

        $categories = $this->service->getTopCategories(30, 10);

        $this->assertCount(2, $categories);
        $this->assertEquals('Hardware', $categories->first()['name']);
        $this->assertEquals(5, $categories->first()['count']);
    }

    public function test_reports_page_loads_for_agent(): void
    {
        $role = Roles::firstOrCreate(['slug' => 'itss'], ['name' => 'ITSS']);
        $agent = User::factory()->create(['role_id' => $role->id]);

        // Test the route exists and responds
        $this->actingAs($agent)
            ->get('/itss/reports/helpdesk')
            ->assertStatus(200)
            ->assertSee('Helpdesk Reports');
    }
}
