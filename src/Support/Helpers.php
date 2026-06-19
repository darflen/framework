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
