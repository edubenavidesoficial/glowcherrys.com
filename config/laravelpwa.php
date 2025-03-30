<?php

return [
    'name' => 'LaravelPWA',
    'manifest' => [
        'name' => env('PWA_SHORT_NAME', 'Mi App PWA'),
        'short_name' => env('PWA_SHORT_NAME', 'MiApp'),
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#000000',
        'display' => 'standalone',
        'orientation'=> 'portrait',
        'status_bar'=> 'black',

        'icons' => [
            '72x72' => [
                'path' => env('PWA_ICON_72', '/images/icons/icon-72x72.png'),
                'purpose' => 'any',
                'extension' => 'png',
            ],
            '96x96' => [
                'path' => env('PWA_ICON_96', '/images/icons/icon-96x96.png'),
                'purpose' => 'any',
                'extension' => 'png',
            ],
            '128x128' => [
                'path' => env('PWA_ICON_128', '/images/icons/icon-128x128.png'),
                'purpose' => 'any',
                'extension' => 'png',
            ],
            '144x144' => [
                'path' => env('PWA_ICON_144', '/images/icons/icon-144x144.png'),
                'purpose' => 'any',
                'extension' => 'png',
            ],
            '152x152' => [
                'path' => env('PWA_ICON_152', '/images/icons/icon-152x152.png'),
                'purpose' => 'any',
                'extension' => 'png',
            ],
            '384x384' => [
                'path' => env('PWA_ICON_384', '/images/icons/icon-384x384.png'),
                'purpose' => 'any',
                'extension' => 'png',
            ],
            '512x512' => [
                'path' => env('PWA_ICON_512', '/images/icons/icon-512x512.png'),
                'purpose' => 'any',
                'extension' => 'png',
            ],
        ],

        'splash' => [
            '640x1136' => env('PWA_SPLASH_640', '/images/icons/splash-640x1136.png'),
            '750x1334' => env('PWA_SPLASH_750', '/images/icons/splash-750x1334.png'),
            '828x1792' => env('PWA_SPLASH_828', '/images/icons/splash-828x1792.png'),
            '1125x2436' => env('PWA_SPLASH_1125', '/images/icons/splash-1125x2436.png'),
            '1242x2208' => env('PWA_SPLASH_1242', '/images/icons/splash-1242x2208.png'),
            '1242x2688' => env('PWA_SPLASH_1242_2', '/images/icons/splash-1242x2688.png'),
            '1536x2048' => env('PWA_SPLASH_1536', '/images/icons/splash-1536x2048.png'),
            '1668x2224' => env('PWA_SPLASH_1668', '/images/icons/splash-1668x2224.png'),
            '2048x2732' => env('PWA_SPLASH_2048', '/images/icons/splash-2048x2732.png'),
        ],

        'shortcuts' => [],
        'custom' => []
    ]
];
