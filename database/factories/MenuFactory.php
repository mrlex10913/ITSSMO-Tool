<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'label' => $this->faker->unique()->words(2, true),
            'section' => $this->faker->randomElement([null, 'General', 'Tools', 'Helpdesk']),
            'route' => $this->faker->boolean(70) ? 'generic.dashboard' : null,
            'url' => null,
            'icon' => $this->faker->randomElement([null, 'home', 'dashboard', 'help']),
            'sort_order' => $this->faker->numberBetween(0, 1000),
            'is_active' => true,
        ];
    }
}
