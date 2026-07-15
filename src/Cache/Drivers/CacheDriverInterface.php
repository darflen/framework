<?php

declare(strict_types=1);

namespace Darflen\Framework\Cache\Drivers;

use DateInterval;

interface CacheDriverInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function getMultiple(array $keys, mixed $default = null): array;

    public function set(string $key, mixed $value, int|DateInterval|null $ttl = null): bool;

    public function setMultiple(array $values, int|DateInterval|null $ttl = null): bool;

    public function delete(string $key): bool;

    public function deleteMultiple(array $keys): bool;

    public function clear(): bool;

    public function has(string $key): bool;
}
