<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Roles;
use App\Services\Menu\MenuBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuBuilderCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_builder_caches_and_clears_by_role_slug(): void
    {
        $role = Roles::firstOrCreate(['slug' => 'pamo'], ['name' => 'PAMO']);

        $m1 = Menu::create([
            'label' => 'First',
            'route' => 'dashboard',
            'url' => null,
            'section' => 'General',
            'icon' => 'home',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        $m1->roles()->sync([$role->id]);

        $m2 = Menu::create([
            'label' => 'Second',
            'route' => null,
            'url' => 'https://example.com',
            'section' => 'Tools',
            'icon' => 'build',
            'sort_order' => 20,
            'is_active' => true,
        ]);
        $m2->roles()->sync([$role->id]);

        $builder = app(MenuBuilder::class);

        // Prime cache
        $items = $builder->getMenuForRoleSlug('pamo');
        $this->assertCount(2, $items);
        $this->assertSame('First', $items[0]['label']);
        $this->assertArrayHasKey('section', $items[0]);

        // Change order in DB triggers observer -> cache is cleared automatically
        $m1->update(['sort_order' => 30]);
        $itemsAfterUpdate = $builder->getMenuForRoleSlug('pamo');
        $this->assertSame('Second', $itemsAfterUpdate[0]['label'], 'Observer should clear cache and reflect new order');

        // Manual clear still works and should be idempotent
        $builder->clearMenuCacheForRoleSlug('pamo');
        $itemsFresh = $builder->getMenuForRoleSlug('pamo');
        $this->assertSame('Second', $itemsFresh[0]['label']);
    }
}
