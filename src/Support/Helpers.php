<?php

declare(strict_types=1);

use Darflen\Framework\Config\Config;

function env(string $key, mixed $default = ''): mixed
{
    return $_ENV[$key] ?? $default;
}

function config(string $key, mixed $default = ''): mixed
{
    return Config::get($key, $default);
}

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
