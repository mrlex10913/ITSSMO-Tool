<?php

namespace Tests\Feature;

use App\Models\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuSeederPamoTest extends TestCase
{
    use RefreshDatabase;

    public function test_pamo_menus_seeded_and_attached(): void
    {
        // Ensure roles exist
        $pamo = Roles::firstOrCreate(['slug' => 'pamo'], ['name' => 'PAMO']);

        $this->seed(\Database\Seeders\MenuSeeder::class);

        $expected = [
            'pamo.dashboard',
            'pamo.helpdesk',
            'pamo.inventory',
            'pamo.barcode',
            'pamo.transactions',
            'pamo.assetTracker',
            'pamo.masterList',
        ];

        $routes = $pamo->menus()->pluck('menus.route')->all();

        foreach ($expected as $route) {
            $this->assertContains($route, $routes);
        }
    }
}
