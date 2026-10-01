<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Roles;
use Illuminate\Database\Seeder;

class PrivilegedAccessMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Create menu item
        $menu = Menu::firstOrCreate(
            ['label' => 'Privileged Access', 'route' => 'controlPanel.reports.privilegedAccess'],
            [
                'icon' => 'shield-check',
                'sort_order' => 110,
                'section' => 'Reports',
                'is_active' => true,
            ]
        );

        // Attach to administrator and developer roles
        $roles = Roles::whereIn('slug', ['administrator', 'developer'])->get();
        $menu->roles()->syncWithoutDetaching($roles->pluck('id')->toArray());

        echo "Menu created: {$menu->label}\n";
        echo "Attached to roles: " . $roles->pluck('name')->implode(', ') . "\n";
    }
}
