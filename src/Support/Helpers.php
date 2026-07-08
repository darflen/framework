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

function normalizePath(string $path): string
{
    $path = str_replace(["/", "\\"], DIRECTORY_SEPARATOR, $path);
    $path = preg_replace('#[\\\/]+#', DIRECTORY_SEPARATOR, $path);
    return trim($path, DIRECTORY_SEPARATOR);
}
