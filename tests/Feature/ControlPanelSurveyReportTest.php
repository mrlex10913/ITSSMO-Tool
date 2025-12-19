<?php

namespace Tests\Feature;

use App\Livewire\ControlPanel\Reports\SurveyReport;
use App\Models\Helpdesk\CsatResponse;
use App\Models\Helpdesk\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ControlPanelSurveyReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function page_renders_with_stats(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['requester_id' => $user->id]);
        CsatResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'requester_email' => $user->email,
            'token' => 't',
            'rating' => 'good',
            'submitted_at' => now(),
        ]);

        Livewire::test(SurveyReport::class)
            ->assertStatus(200)
            ->assertViewHas('stats', function ($stats) {
                return isset($stats['count']) && $stats['count'] === 1 && ($stats['avg'] ?? null) !== null;
            });
    }
}
