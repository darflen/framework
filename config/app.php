<?php

return [
    'name' => env('APP_NAME', 'Darflen'),
    'maintenance' => env('APP_MAINTENANCE', false),
    'fallback_language' => env('APP_FALLBACK_LOCALE', 'en'),
    'links' => [
        'root' => env('APP_URL_ROOT', 'http://localhost'),
        'api' => env('APP_URL_API', 'http://api.localhost'),
        'static' => env('APP_URL_STATIC', 'http://static.localhost'),
    ]
];
