<?php

declare(strict_types=1);

use Darflen\Framework\App\App;
use Darflen\Framework\Translation\Translator;
use Psr\Container\ContainerInterface;

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
        $path = str_replace(['/', '\\'], '/', $path);
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

if (!function_exists('safePhpShell')) {
    /**
     * Create a new php instance and run a PHP file
     *
     * DO NOT LET USER INPUTS IN THAT
     *
     * @param  string $file
     * @param  array $data
     * @return string|false|null
     */
    function safePhpShell(string $file, array $data): string|false|null
    {
        return shell_exec('php -f ' . escapeshellarg(normalizePath($file)) . ' ' . escapeshellarg(jsonEncodeBase64($data)));
    }
}

if (!function_exists('jsonEncodeBase64')) {
    function jsonEncodeBase64(array $data): string
    {
        return base64_encode(json_encode($data));
    }
}

if (!function_exists('jsonDecodeBase64')) {
    function jsonDecodeBase64(string $json): array
    {
        return json_decode(base64_decode($json), true);
    }
}

if (!function_exists('container')) {
    function container(): ContainerInterface
    {
        return App::getInstance()->getContainer();
    }
}

if (!function_exists('trans')) {
    function trans(string $key, array $data = []): mixed
    {
        return container()->get(Translator::class)->translate($key, $data);
    }
}

if (!function_exists('transPlural')) {
    function transPlural(string $key, ?int $count = null, array $data = []): mixed
    {
        return container()->get(Translator::class)->translatePlural($key, $count, $data);
    }
}

if (!function_exists('__')) {
    function __(string $key, array $data = []): mixed
    {
        return trans($key, $data);
    }
}

if (!function_exists('___')) {
    function ___(string $key, ?int $count = null, array $data = []): mixed
    {
        return transPlural($key, $count, $data);
    }
}

if (!function_exists('asyncWork')) {
    /**
     * Run an asyncronous task
     *
     * Only work with the Darflen skeleton! DO NOT LET USER INPUTS IN THAT
     */
    function asyncWork(string $job, array $data): string|false|null
    {
        $data['_job'] = $job;
        return safePhpShell(App::getInstance()->getProjectDir() . '/bootstrap/worker.php', $data);
    }
}
