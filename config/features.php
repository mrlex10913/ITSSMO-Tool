<?php

return [
    'roles' => [
        // Keep explicit dashboards unchanged via routes; config here is used mainly for menus
        'administrator' => [
            'home' => 'dashboard',
            'menu' => [
                ['label' => 'Main Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
            ],
        ],
        'developer' => [
            'home' => 'dashboard',
            'menu' => [
                ['label' => 'Main Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
            ],
        ],
        'pamo' => [
            'home' => 'pamo.dashboard',
            'menu' => [
                ['label' => 'Dashboard', 'route' => 'pamo.dashboard', 'icon' => 'monitor'],
            ],
        ],
        'bfo' => [
            'home' => 'bfo.dashboard',
            'menu' => [
                ['label' => 'Dashboard', 'route' => 'bfo.dashboard', 'icon' => 'monitor'],
            ],
        ],
        'itss' => [
            'home' => 'itss.dashboard',
            'menu' => [
                ['label' => 'Dashboard', 'route' => 'itss.dashboard', 'icon' => 'monitor'],
            ],
        ],

        // Fallback for any new roles
        'user' => [
            'home' => 'generic.dashboard',
            'menu' => [
                ['label' => 'Helpdesk', 'route' => 'tickets.index', 'icon' => 'assignment'],
                ['label' => 'Profile', 'route' => 'profile.show', 'icon' => 'person'],
            ],
        ],
    ],
];
