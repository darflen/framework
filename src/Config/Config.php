<?php

declare(strict_types=1);

namespace Darflen\Framework\Config;

use Darflen\Framework\Support\Arr;
use Dotenv\Dotenv;

class Config
{
    public static string $configDirectory = '';

    private static array $configs = [];

    private static string $envDirectory;

    public static function setup(string $configDirectory, string $envDirectory): self
    {
        self::$configDirectory = $configDirectory;
        self::$envDirectory = $envDirectory;
        return new self();
    }

    public function create(): void
    {
        $dotenv = Dotenv::createImmutable(self::$envDirectory);
        $dotenv->safeLoad();
        $files = glob(self::$configDirectory . '/*.php');
        foreach ($files as $file) {
            $fileName = basename($file, '.php');
            self::$configs[$fileName] = include $file;
        }
    }

    public static function set(string $key, mixed $value = ''): void
    {
        Arr::set(self::$configs, $key, $value);
    }

    public static function get(string $key, mixed $default = ''): mixed
    {
        return Arr::get(self::$configs, $key, $default);
    }

    public static function all(): array
    {
        return self::$configs;
    }
}
