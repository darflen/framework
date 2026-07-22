<?php

return [
    'mariadb' => [
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => (int) env('DB_PORT', 3306),
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
        ],
    ],
    'redis' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => (int) env('REDIS_PORT', 6379),
        'database' => (int) env('REDIS_DATABASE', 0),
        'username' => env('REDIS_USERNAME', ''),
        'password' => env('REDIS_PASSWORD', ''),
        'read_write_timeout' => (int) env('REDIS_READ_WRITE_TIMEOUT', 30),
        'persistent' => env('REDIS_PERSISTENT', false),
        'persistent_id' => env('REDIS_PERSISTENT_ID', 'default'),
        'options' => [
            Redis::OPT_PREFIX => env('REDIS_PREFIX', ''),
        ],
    ],
];
