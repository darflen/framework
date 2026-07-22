<?php

return [
    'directory' => env('LOG_DIRECTORY', '/storage/logs'),
    'extension' => env('LOG_EXTENSION', 'log'),
    'file_date_format' => 'Y-m-d',
    'log_date_format' => 'Y-m-d H:i:s.u',
    'level' => env('LOG_LEVEL', 'debug'),
];
