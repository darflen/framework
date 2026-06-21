<?php

return [
    'ffmpeg' => [
        'processor' => env('FFMPEG_PROCESSOR', ''),
        'probe' => env('FFMPEG_PROBE', ''),
        'timeout' => env('FFMPEG_TIMEOUT', 3600),
        'threads' => env('FFMPEG_THREADS', 12),
    ]
];
