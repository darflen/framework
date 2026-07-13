<?php

declare(strict_types=1);

if (!function_exists('env')) {
    function env(string $key, mixed $default = ''): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('getDrivePath')) {
    function getDrivePath(string $path): string
    {
        $drive = '';
        if (preg_match('/^[a-zA-Z]:/', $path)) {
            $drive = substr($path, 0, 2) . '/';
        } elseif (strpos($path, '/') === 0) {
            $drive = '/';
        }
        return $drive;
    }
}

if (!function_exists('NormalizePath')) {
    function normalizePath(string $path): string
    {
        $drive = getDrivePath($path);
        if ($drive !== '/' && $drive !== '') {
            $path = substr($path, 2);
        }
        $path = str_replace(["/", "\\"], '/', $path);
        $path = preg_replace('#[\\\/]+#', '/', $path);
        $parts = explode('/', $path);
        $absolutes = [];
        foreach ($parts as $part) {
            if ($part === '.' || $part === '') {
                continue;
            }
            if ($part === '..') {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        $path = $drive . implode('/', $absolutes);
        return $path;
    }
}

if (!function_exists('dd')) {
    function dd(mixed $data): void
    {
        var_dump($data);
        die();
    }
}
