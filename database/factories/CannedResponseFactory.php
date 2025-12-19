<?php

namespace Database\Factories;

use App\Models\Helpdesk\CannedResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

class CannedResponseFactory extends Factory
{
    protected $model = CannedResponse::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'body' => fake()->paragraph(),
            'is_global' => true,
        ];
    }
}
