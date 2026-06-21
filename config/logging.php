<?php

return [
    'directory' => env('LOG_DIRECTORY', '/storage/logs'),
    'extension' => env('LOG_EXTENSION', 'log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'keeptime' => env('LOG_KEEPTIME', 180),
];
