<?php

return [
    'name' => env('APP_NAME', 'Darflen'),
    'env' => env('APP_ENV', 'dev'),
    'maintenance' => env('APP_MAINTENANCE', false),
    'default_language' => env('APP_DEFAULT_LANGUAGE', 'en'),
    'fallback_language' => env('APP_FALLBACK_LOCALE', 'en'),
    'links' => [
        'root' => env('APP_URL_ROOT', 'http://localhost'),
        'api' => env('APP_URL_API', 'http://api.localhost'),
        'static' => env('APP_URL_STATIC', 'http://static.localhost'),
    ],
];
