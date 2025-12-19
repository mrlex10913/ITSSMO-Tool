<?php

namespace Database\Factories;

use App\Models\Roles;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Roles>
 */
class RolesFactory extends Factory
{
    protected $model = Roles::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['Admin', 'Manager', 'Staff', 'End User']);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'is_default' => false,
        ];
    }
}
