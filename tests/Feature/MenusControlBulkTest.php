<?php

namespace Tests\Feature;

use App\Livewire\ControlPanel\MenusControl;
use App\Models\Menu;
use App\Models\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MenusControlBulkTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_activate_deactivate_updates_records(): void
    {
        $role = Roles::firstOrCreate(['slug' => 'itss'], ['name' => 'ITSS']);
        $m1 = Menu::create(['label' => 'A', 'route' => 'a', 'is_active' => false]);
        $m2 = Menu::create(['label' => 'B', 'route' => 'b', 'is_active' => false]);
        $m1->roles()->sync([$role->id]);
        $m2->roles()->sync([$role->id]);

        Livewire::test(MenusControl::class)
            ->set('selected', [$m1->id, $m2->id])
            ->call('bulkSetActive', true)
            ->assertSet('selected', []);

        $this->assertTrue((bool) Menu::find($m1->id)->is_active);
        $this->assertTrue((bool) Menu::find($m2->id)->is_active);

        Livewire::test(MenusControl::class)
            ->set('selected', [$m1->id])
            ->call('bulkSetActive', false)
            ->assertSet('selected', []);

        $this->assertFalse((bool) Menu::find($m1->id)->is_active);
        $this->assertTrue((bool) Menu::find($m2->id)->is_active);
    }
}
