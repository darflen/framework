<?php

return [
    'mariadb' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', 3306),
        'database' => env('DB_DATABASE', 'darflen_db'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        'prefix' => env('DB_PREFIX', ''),
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_TO_STRING,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],
    'redis' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DATABASE', 0),
        'username' => env('REDIS_USERNAME', ''),
        'password' => env('REDIS_PASSWORD', ''),
        'scheme' => env('REDIS_SCHEME', 'tcp'),
        'read_write_timeout' => env('REDIS_READ_WRITE_TIMEOUT', 30),
        'options' => [
            'prefix' => env('REDIS_PREFIX', ''),
            'persistent' => env('REDIS_PERSISTENT', false)
        ]
    ]
];
