<?php

namespace Tests\Feature;

use App\Livewire\ControlPanel\MenusControl;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MenusControlAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_developer_can_access_menus_control(): void
    {
        $role = Roles::firstOrCreate(['slug' => 'developer'], ['name' => 'Developer']);
        $user = User::factory()->create(['role_id' => $role->id]);

        $this->actingAs($user);

        Livewire::test(MenusControl::class)
            ->assertStatus(200);
    }
}
