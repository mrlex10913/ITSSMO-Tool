<?php

namespace Tests\Feature;

use App\Livewire\ControlPanel\MenusControl;
use App\Models\Menu;
use App\Models\Roles;
use App\Services\Menu\MenuBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MenusControlRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_assign_and_remove_roles_and_cache_clear(): void
    {
        $pamo = Roles::firstOrCreate(['slug' => 'pamo'], ['name' => 'PAMO']);
        $itss = Roles::firstOrCreate(['slug' => 'itss'], ['name' => 'ITSS']);

        $menu = Menu::create(['label' => 'Test', 'route' => 'dashboard', 'is_active' => true]);
        $menu->roles()->sync([$pamo->id]);

        // Prime cache
        $builder = app(MenuBuilder::class);
        $this->assertNotEmpty($builder->getMenuForRoleSlug('pamo'));

        // Bulk assign ITSS to the same menu
        Livewire::test(MenusControl::class)
            ->set('selected', [$menu->id])
            ->set('bulk_role_ids', [$itss->id])
            ->call('bulkAssignRoles')
            ->assertSet('selected', [])
            ->assertSet('bulk_role_ids', []);

        $this->assertTrue($menu->fresh()->roles()->where('roles.id', $itss->id)->exists());

        // Update menu to trigger observer; ensure cache rebuild without errors
        $menu->update(['label' => 'Test Updated']);
        $this->assertNotEmpty($builder->getMenuForRoleSlug('itss'));

        // Bulk remove ITSS
        Livewire::test(MenusControl::class)
            ->set('selected', [$menu->id])
            ->set('bulk_role_ids', [$itss->id])
            ->call('bulkRemoveRoles');

        $this->assertFalse($menu->fresh()->roles()->where('roles.id', $itss->id)->exists());
    }
}
