<?php

return [
    'encryption' => [
        'cipher' => env('SECURITY_CIPHER', 'AES-256-GCM'),
        'key' => env('SECURITY_KEY', ''),
        'mac' => env('SECURITY_MAC_ALGORITHM', ''),
    ],
    'hashing' => [
        'algorithm' => env('SECURITY_MAC_ALGORITHM', PASSWORD_BCRYPT),
        PASSWORD_BCRYPT => [
            'rounds' => env('BCRYPT_ROUNDS', 16)
        ],
        PASSWORD_ARGON2ID => [
            'threads' => env('ARGON2_THREADS', 12),
            'memory_cost' => env('ARGON2_MEMORY_COST', 1048576),
            'time_cost' => env('ARGON2_TIME_COST', 4),
        ]
    ]
];
