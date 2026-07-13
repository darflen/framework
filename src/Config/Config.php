<?php

declare(strict_types=1);

namespace Darflen\Framework\Config;

use Darflen\Framework\Support\Arr;
use Dotenv\Dotenv;
use InvalidArgumentException;

class Config
{
    private array $configs = [];

    public function loadConfigDirectory(string $directory): void
    {
        $directory = normalizePath($directory);
        if (!is_dir($directory) || !is_writable($directory)) {
            throw new InvalidArgumentException('Config path is not a directory or is not writable');
        }
        $files = glob($directory . '/*.php');
        foreach ($files as $file) {
            $this->loadConfigFile($file);
        }
    }

    public function loadConfigFile(string $path): void
    {
        $path = normalizePath($path);
        if (is_file($path)) {
            $fileName = basename($path, '.php');
            $config = require $path;
            $this->configs[$fileName] ??= [];
            $this->configs[$fileName] = array_merge($this->configs[$fileName], $config);
            return;
        }
        throw new InvalidArgumentException('Config path is not a file');
    }

    public function loadConfigArray(string $name, array $config): void
    {
        $this->configs[$name] ??= [];
        $this->configs[$name] = array_merge($this->configs[$name], $config);
    }

    public function loadEnv(string $path, string|array|null $names = null): void
    {
        $dotenv = Dotenv::createMutable(normalizePath($path), $names);
        $dotenv->load();
    }

    public function set(string $key, mixed $value = ''): void
    {
        Arr::set($this->configs, $key, $value);
    }

    public function get(string $key, mixed $default = ''): mixed
    {
        return Arr::get($this->configs, $key, $default);
    }

    public function remove(string $key): void
    {
        Arr::remove($this->configs, $key);
    }

    public function all(): array
    {
        return $this->configs;
    }
}
