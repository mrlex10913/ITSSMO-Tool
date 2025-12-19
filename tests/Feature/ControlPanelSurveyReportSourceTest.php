<?php

namespace Tests\Feature;

use App\Livewire\ControlPanel\Reports\SurveyReport;
use App\Models\Helpdesk\CsatResponse;
use App\Models\Helpdesk\Ticket;
use App\Models\SystemCsatResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ControlPanelSurveyReportSourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function filter_by_guest_only_counts_ticket_csat(): void
    {
        $user = User::factory()->create(['department' => 'it']);
        $ticket = Ticket::factory()->create(['requester_id' => $user->id, 'department' => 'it']);
        CsatResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'requester_email' => $user->email,
            'token' => 't',
            'rating' => 'good',
            'submitted_at' => now(),
        ]);
        SystemCsatResponse::create([
            'user_id' => $user->id,
            'rating' => 2,
            'comment' => 'meh',
            'submitted_at' => now(),
        ]);

        Livewire::test(SurveyReport::class)
            ->set('source', 'guest')
            ->assertStatus(200)
            ->assertViewHas('stats', function ($stats) {
                return ($stats['count'] ?? 0) === 1 && ($stats['avg'] ?? null) !== null;
            });
    }

    /** @test */
    public function filter_by_end_user_only_counts_system_csat(): void
    {
        $user = User::factory()->create(['department' => 'it']);
        $ticket = Ticket::factory()->create(['requester_id' => $user->id, 'department' => 'it']);
        CsatResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'requester_email' => $user->email,
            'token' => 't',
            'rating' => 'good',
            'submitted_at' => now(),
        ]);
        SystemCsatResponse::create([
            'user_id' => $user->id,
            'rating' => 2,
            'comment' => 'meh',
            'submitted_at' => now(),
        ]);

        Livewire::test(SurveyReport::class)
            ->set('source', 'end_user')
            ->assertStatus(200)
            ->assertViewHas('stats', function ($stats) {
                return ($stats['count'] ?? 0) === 1 && ($stats['avg'] ?? null) !== null && empty($stats['perAgent'] ?? []) && empty($stats['perCategory'] ?? []);
            });
    }
}
