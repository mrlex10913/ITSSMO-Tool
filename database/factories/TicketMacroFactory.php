<?php

namespace Database\Factories;

use App\Models\Helpdesk\TicketMacro;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketMacroFactory extends Factory
{
    protected $model = TicketMacro::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(2),
            'actions' => ['status' => 'resolved'],
            'is_active' => true,
        ];
    }
}
