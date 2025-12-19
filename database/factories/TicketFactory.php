<?php

namespace Database\Factories;

use App\Models\Helpdesk\Ticket;
use App\Models\Helpdesk\TicketCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'ticket_no' => 'HD-'.now()->format('Y').'-'.str_pad((string) fake()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'subject' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'type' => 'incident',
            'status' => 'open',
            'priority' => 'medium',
            // Ensure relational integrity for tests that don't specify these fields
            'requester_id' => User::factory(),
            'category_id' => function () {
                // Create or reuse a basic category
                return TicketCategory::factory()->create()->id;
            },
        ];
    }

    /**
     * Guest-submitted ticket (no requester user).
     */
    public function guest(): self
    {
        return $this->state(function () {
            return [
                'requester_id' => null,
            ];
        });
    }
}
