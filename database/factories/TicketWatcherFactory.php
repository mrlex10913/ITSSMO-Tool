<?php

namespace Database\Factories;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketWatcher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketWatcher>
 */
class TicketWatcherFactory extends Factory
{
    protected $model = TicketWatcher::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'role' => 'watcher',
            'notify_comment' => true,
            'notify_status' => true,
            'notify_assignment' => true,
            'notify_escalation' => true,
        ];
    }
}
