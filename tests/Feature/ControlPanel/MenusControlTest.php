<?php

namespace Tests\Feature\ControlPanel;

use App\Livewire\ControlPanel\MenusControl;
use App\Models\Menu;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MenusControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Minimal roles (idempotent for seeded DBs)
        $admin = Roles::firstOrCreate(['slug' => 'administrator'], ['name' => 'Administrator']);
        Roles::firstOrCreate(['slug' => 'pamo'], ['name' => 'PAMO']);
        // Admin user
        User::factory()->create(['role_id' => $admin->id]);
        $this->actingAs(User::first());
    }

    public function test_validation_requires_route_or_url(): void
    {
        Livewire::test(MenusControl::class)
            ->set('label', 'Test Menu')
            ->set('route', null)
            ->set('url', null)
            ->call('create')
            ->assertHasErrors(['route' => 'required_without', 'url' => 'required_without']);
    }

    public function test_search_is_scoped_and_roles_eager_loaded(): void
    {
        $pamo = Roles::where('slug', 'pamo')->first();
        $m1 = Menu::factory()->create(['label' => 'Alpha Menu', 'route' => 'generic.dashboard']);
        $m1->roles()->attach($pamo->id);

        $component = Livewire::test(MenusControl::class)
            ->set('search', 'Alpha')
            ->set('departmentFilter', 'pamo');

        $menus = $component->instance()->menus;
        $this->assertTrue($menus->count() >= 1);
        $this->assertEquals('Alpha Menu', $menus->first()->label);
        // Ensure roles relationship is loaded (no lazy call needed)
        $this->assertTrue($menus->first()->relationLoaded('roles'));
    }
}
