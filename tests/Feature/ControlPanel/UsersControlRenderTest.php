<?php

namespace Tests\Feature\ControlPanel;

use App\Livewire\ControlPanel\UsersControl;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UsersControlRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_component_renders_and_shows_menu_access_section(): void
    {
        $role = Roles::factory()->create(['name' => 'Admin']);
        User::factory()->create(['role_id' => $role->id]);

        Livewire::test(UsersControl::class)
            ->assertStatus(200)
            ->assertSee('User Management')
            ->assertSee('Menu Access');
    }
}
