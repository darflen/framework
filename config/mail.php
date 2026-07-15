<?php

return [
    'stmp' => [
        'host' => env('EMAIL_HOST', '127.0.0.1'),
        'auth' => env('EMAIL_AUTH', true),
        'port' => env('EMAIL_PORT', 587),
        'security' => env('EMAIL_SECURITY', 'tls'),
        'username' => env('EMAIL_USERNAME', 'user@example.com'),
        'password' => env('EMAIL_PASSWORD', ''),
    ],
    'from' => [
        'address' => env('EMAIL_ADDRESS', 'example@example.com'),
        'name' => env('APP_NAME', 'Darflen'),
    ],
];
