<?php

return [
    'default' => env('BROADCAST_CONNECTION', env('BROADCAST_DRIVER', 'log')),

    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'host' => env('PUSHER_HOST', '127.0.0.1'),
                'port' => env('PUSHER_PORT', 6001),
                'scheme' => env('PUSHER_SCHEME', 'http'),
                'useTLS' => env('PUSHER_SCHEME', 'http') === 'https',
            ],
        ],

    'reverb' => [
            'driver' => 'reverb',
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'app_id' => env('REVERB_APP_ID'),
            'options' => [
                'host' => env('REVERB_HOST', '127.0.0.1'),
                'port' => env('REVERB_PORT', 6001),
                // Server-side HTTP requests must use http/https, not ws/wss
                // Map any ws/wss values to http/https respectively
                'scheme' => (function () {
                    $s = env('REVERB_HTTP_SCHEME');
                    if ($s) { return $s; }
                    $raw = env('REVERB_SCHEME', 'ws');
                    return $raw === 'wss' ? 'https' : 'http';
                })(),
                'useTLS' => in_array(env('REVERB_HTTP_SCHEME', env('REVERB_SCHEME', 'ws')), ['https', 'wss'], true),
            ],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
