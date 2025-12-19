<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'sort_order' => $this->faker->numberBetween(0, 1000),
            'is_active' => true,
            'is_guest_visible' => true,
        ];
    }

    public function hiddenFromGuests(): self
    {
        return $this->state(fn () => ['is_guest_visible' => false]);
    }

    public function inactive(): self
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
