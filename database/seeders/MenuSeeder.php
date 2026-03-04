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
                    'section' => $menuData['section'] ?? null,
                    'is_active' => true,
                ]
            );
            $roleIds = Roles::whereIn('slug', $roleSlugs)->pluck('id')->all();
            if (! empty($roleIds)) {
                $menu->roles()->syncWithoutDetaching($roleIds);
            }
        };

        // ===================================================================
        // CORE DASHBOARDS (Each department's main entry point)
        // ===================================================================
        $attach(['label' => 'Main Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard', 'sort_order' => 1, 'section' => 'Dashboard'], ['administrator', 'developer']);
        $attach(['label' => 'ITSS Dashboard', 'route' => 'itss.dashboard', 'icon' => 'monitor', 'sort_order' => 1, 'section' => 'Dashboard'], ['itss']);
        $attach(['label' => 'PAMO Dashboard', 'route' => 'pamo.dashboard', 'icon' => 'monitor', 'sort_order' => 1, 'section' => 'Dashboard'], ['pamo']);
        $attach(['label' => 'BFO Dashboard', 'route' => 'bfo.dashboard', 'icon' => 'monitor', 'sort_order' => 1, 'section' => 'Dashboard'], ['bfo']);

        // ===================================================================
        // HELPDESK LINKS (Shared across departments)
        // ===================================================================
        $attach(['label' => 'Helpdesk', 'route' => 'itss.helpdesk', 'icon' => 'assignment', 'sort_order' => 5], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'Helpdesk', 'route' => 'pamo.helpdesk', 'icon' => 'assignment', 'sort_order' => 5], ['pamo']);
        $attach(['label' => 'Helpdesk', 'route' => 'bfo.helpdesk', 'icon' => 'assignment', 'sort_order' => 5], ['bfo']);

        // ===================================================================
        // PAMO DEPARTMENT - Property and Asset Management Office
        // ===================================================================
        $attach(['label' => 'Inventory', 'route' => 'pamo.inventory', 'icon' => 'inventory_2', 'sort_order' => 10, 'section' => 'PAMO'], ['pamo']);
        $attach(['label' => 'Barcode', 'route' => 'pamo.barcode', 'icon' => 'qr_code_2', 'sort_order' => 11, 'section' => 'PAMO'], ['pamo']);
        $attach(['label' => 'Transactions', 'route' => 'pamo.transactions', 'icon' => 'swap_horiz', 'sort_order' => 12, 'section' => 'PAMO'], ['pamo']);
        $attach(['label' => 'Assets Tracker', 'route' => 'pamo.assetTracker', 'icon' => 'track_changes', 'sort_order' => 13, 'section' => 'PAMO'], ['pamo']);
        $attach(['label' => 'Master List', 'route' => 'pamo.masterList', 'icon' => 'list_alt', 'sort_order' => 14, 'section' => 'PAMO'], ['pamo']);

        // ===================================================================
        // BFO DEPARTMENT - Budget and Finance Office
        // ===================================================================
        $attach(['label' => 'Cheque Management', 'route' => 'bfo.cheque', 'icon' => 'payments', 'sort_order' => 10, 'section' => 'BFO'], ['bfo']);
        $attach(['label' => 'Cheque List', 'route' => 'bfo.cheque-list', 'icon' => 'receipt_long', 'sort_order' => 11, 'section' => 'BFO'], ['bfo']);

        // ===================================================================
        // ITSS DEPARTMENT - IT Services and Solutions
        // ===================================================================
        $attach(['label' => 'SLA Policies', 'route' => 'itss.sla.policies', 'icon' => 'settings_suggest', 'sort_order' => 10, 'section' => 'ITSS'], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'SLA Insights', 'route' => 'itss.sla.insights', 'icon' => 'insights', 'sort_order' => 11, 'section' => 'ITSS'], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'Escalations', 'route' => 'itss.escalations', 'icon' => 'warning', 'sort_order' => 12, 'section' => 'ITSS'], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'Canned Responses', 'route' => 'itss.canned', 'icon' => 'quickreply', 'sort_order' => 13, 'section' => 'ITSS'], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'Macros', 'route' => 'itss.macros', 'icon' => 'bolt', 'sort_order' => 14, 'section' => 'ITSS'], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'Assignment Rules', 'route' => 'itss.assignment-rules', 'icon' => 'rule', 'sort_order' => 15, 'section' => 'ITSS'], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'SLA Escalations', 'route' => 'itss.sla.escalations', 'icon' => 'escalator_warning', 'sort_order' => 16, 'section' => 'ITSS'], ['itss', 'administrator', 'developer']);
        $attach(['label' => 'ISO Audit Report', 'route' => 'itss.reports.iso-audit', 'icon' => 'summarize', 'sort_order' => 17, 'section' => 'ITSS'], ['itss', 'administrator', 'developer']);

        // ===================================================================
        // DOCUMENT LIBRARY (Shared across departments)
        // ===================================================================
        $attach(['label' => 'Document Library', 'route' => 'document-library.dashboard', 'icon' => 'folder_managed', 'sort_order' => 20, 'section' => 'Documents'], ['itss', 'pamo', 'bfo', 'administrator', 'developer']);
        // NOTE: Upload Document menu removed - upload is accessible via Dashboard and My Drive buttons
        $attach(['label' => 'Search Documents', 'route' => 'document-library.search', 'icon' => 'search', 'sort_order' => 22, 'section' => 'Documents'], ['itss', 'pamo', 'bfo', 'administrator', 'developer']);
        $attach(['label' => 'Storage Locations', 'route' => 'document-library.storage-locations', 'icon' => 'storage', 'sort_order' => 23, 'section' => 'Documents'], ['administrator', 'developer']);

        // ===================================================================
        // ADMIN PANEL (Administrator & Developer only)
        // ===================================================================
        $attach(['label' => 'Control Panel', 'route' => 'controlPanel.admin', 'icon' => 'admin_panel_settings', 'sort_order' => 50, 'section' => 'Admin'], ['administrator', 'developer']);
        $attach(['label' => 'Menu Controller', 'route' => 'controlPanel.menus', 'icon' => 'tune', 'sort_order' => 51, 'section' => 'Admin'], ['administrator', 'developer']);
        $attach(['label' => 'Roles', 'route' => 'controlPanel.roles', 'icon' => 'group', 'sort_order' => 52, 'section' => 'Admin'], ['administrator', 'developer']);
    }
}
