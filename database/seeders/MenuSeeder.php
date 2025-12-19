<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Roles;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Helper to create/find a menu and attach to role slugs that exist
        $attach = function (array $menuData, array $roleSlugs): void {
            // Ensure distinct rows per route/url so departments can have their own Helpdesk entries
            $unique = [
                'label' => $menuData['label'],
                'route' => $menuData['route'] ?? null,
                'url' => $menuData['url'] ?? null,
            ];

            $menu = Menu::firstOrCreate(
                $unique,
                [
                    'icon' => $menuData['icon'] ?? 'menu',
                    'sort_order' => $menuData['sort_order'] ?? 10,
                    'is_active' => true,
                ]
            );
            $roleIds = Roles::whereIn('slug', $roleSlugs)->pluck('id')->all();
            if (! empty($roleIds)) {
                $menu->roles()->syncWithoutDetaching($roleIds);
            }
        };

        // Core dashboards
        $attach(['label' => 'Main Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard', 'sort_order' => 1], ['administrator', 'developer']);
        $attach(['label' => 'ITSS Dashboard', 'route' => 'itss.dashboard', 'icon' => 'monitor', 'sort_order' => 1], ['itss']);
        $attach(['label' => 'PAMO Dashboard', 'route' => 'pamo.dashboard', 'icon' => 'monitor', 'sort_order' => 1], ['pamo']);
        $attach(['label' => 'BFO Dashboard', 'route' => 'bfo.dashboard', 'icon' => 'monitor', 'sort_order' => 1], ['bfo']);

        // Helpdesk links
        $attach(['label' => 'Helpdesk', 'route' => 'itss.helpdesk', 'icon' => 'assignment', 'sort_order' => 5], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'Helpdesk', 'route' => 'pamo.helpdesk', 'icon' => 'assignment', 'sort_order' => 5], ['pamo']);
        $attach(['label' => 'Helpdesk', 'route' => 'bfo.helpdesk', 'icon' => 'assignment', 'sort_order' => 5], ['bfo']);

        // PAMO feature links
        $attach(['label' => 'Inventory', 'route' => 'pamo.inventory', 'icon' => 'inventory_2', 'sort_order' => 10], ['pamo']);
        $attach(['label' => 'Barcode', 'route' => 'pamo.barcode', 'icon' => 'qr_code_2', 'sort_order' => 11], ['pamo']);
        $attach(['label' => 'Transactions', 'route' => 'pamo.transactions', 'icon' => 'swap_horiz', 'sort_order' => 12], ['pamo']);
        $attach(['label' => 'Assets Tracker', 'route' => 'pamo.assetTracker', 'icon' => 'track_changes', 'sort_order' => 13], ['pamo']);
        $attach(['label' => 'Master List', 'route' => 'pamo.masterList', 'icon' => 'list_alt', 'sort_order' => 14], ['pamo']);

        // ITSS tools
        $attach(['label' => 'SLA Policies', 'route' => 'itss.sla.policies', 'icon' => 'settings_suggest', 'sort_order' => 10], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'SLA Insights', 'route' => 'itss.sla.insights', 'icon' => 'insights', 'sort_order' => 11], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'Escalations', 'route' => 'itss.escalations', 'icon' => 'warning', 'sort_order' => 12], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'Canned Responses', 'route' => 'itss.canned', 'icon' => 'quickreply', 'sort_order' => 13], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'Macros', 'route' => 'itss.macros', 'icon' => 'bolt', 'sort_order' => 14], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'Assignment Rules', 'route' => 'itss.assignment-rules', 'icon' => 'rule', 'sort_order' => 15], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'SLA Escalations', 'route' => 'itss.sla.escalations', 'icon' => 'escalator_warning', 'sort_order' => 16], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'ISO Audit Report', 'route' => 'itss.reports.iso-audit', 'icon' => 'summarize', 'sort_order' => 17], ['itss', 'administrator', 'developer']);

        // Admin panel
        $attach(['label' => 'Control Panel', 'route' => 'controlPanel.admin', 'icon' => 'admin_panel_settings', 'sort_order' => 50], ['administrator', 'developer']);
        $attach(['label' => 'Menu Controller', 'route' => 'controlPanel.menus', 'icon' => 'tune', 'sort_order' => 51], ['administrator', 'developer']);
        $attach(['label' => 'Roles', 'route' => 'controlPanel.roles', 'icon' => 'group', 'sort_order' => 52], ['administrator', 'developer']);
    }
}
