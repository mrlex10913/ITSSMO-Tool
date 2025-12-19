<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Roles;
use App\Models\User;
use App\Services\Menu\MenuBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuBuilderUserSpecificTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_specific_menus_override_role_menus(): void
    {
        $role = Roles::firstOrCreate(['slug' => 'pamo'], ['name' => 'PAMO']);
        $user = User::factory()->create(['role_id' => $role->id]);

        // Role menu
        $roleMenu = Menu::create(['label' => 'Role Item', 'route' => 'dashboard', 'sort_order' => 10, 'is_active' => true]);
        $roleMenu->roles()->sync([$role->id]);

        // User-specific menu
        $userMenu = Menu::create(['label' => 'User Item', 'route' => 'tickets.index', 'sort_order' => 5, 'is_active' => true]);
        $userMenu->users()->sync([$user->id]);

        $builder = app(MenuBuilder::class);
        $items = $builder->getMenuFor($user);
        $this->assertNotEmpty($items);
        $this->assertSame('User Item', $items[0]['label'], 'User-specific menu should take precedence');
    }
}
