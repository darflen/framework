<?php

return [
    'directory' => env('LOG_DIRECTORY', '/storage/logs'),
    'extension' => env('LOG_EXTENSION', 'log'),
    'fileDateFormat' => 'Y-m-d',
    'logDateFormat' => 'Y-m-d H:i:s.u',
    'level' => env('LOG_LEVEL', 'debug'),
    'keeptime' => env('LOG_KEEPTIME', 180),
];
