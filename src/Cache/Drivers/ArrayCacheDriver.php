<?php

namespace Darflen\Framework\Cache\Drivers;

use Darflen\Framework\Support\Arr;
use DateInterval;
use DateTimeInterface;
use Override;

class ArrayCacheDriver implements CacheDriverInterface
{
    /**
     * @var array
     */
    private $array;

    private DateTimeInterface $dateTime;

    private const STRING CACHE_KEY = 'CACHE_DATA';

    public function __construct(?array &$array, DateTimeInterface $dateTime)
    {
        $this->dateTime = $dateTime;
        $this->array = &$array;
    }

    protected function parseTTL(int|DateInterval|null $ttl): ?int
    {
        if ($ttl instanceof DateInterval) {
            $ttl = date_create('@0')->add($ttl)->getTimestamp();
        }
        return $ttl;
    }

    protected function isExpired(?int $expiration): bool
    {
        if (is_null($expiration)) {
            return false;
        }
        return $this->dateTime->getTimestamp() >= $expiration;
    }

    protected function getCacheKey(string $key): string
    {
        return self::CACHE_KEY . '_' . $key;
    }

    #[Override]
    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->getCacheKey($key);
        $result = Arr::get($this->array, $key, $default);
        $value = $result[$this->getCacheKey('value')] ?? $default;
        $expiration = $result[$this->getCacheKey('expiration')] ?? null;
        if ($this->isExpired($expiration)) {
            Arr::remove($this->array, $key);
            return $default;
        }
        return $value;
    }

    #[Override]
    public function getMultiple(array $keys, mixed $default = null): array
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }
        return $results;
    }

    #[Override]
    public function set(string $key, mixed $value, int|DateInterval|null $ttl = null): bool
    {
        $key = $this->getCacheKey($key);
        $ttl = $this->parseTTL($ttl);
        Arr::set($this->array, $key, [
            $this->getCacheKey('value') => $value,
            $this->getCacheKey('expiration') => is_null($ttl) ? null : $this->dateTime->getTimestamp() + $ttl,
        ]);
        return true;
    }

    #[Override]
    public function setMultiple(array $values, int|DateInterval|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    #[Override]
    public function delete(string $key): bool
    {
        $key = $this->getCacheKey($key);
        Arr::remove($this->array, $key);
        return true;
    }

    #[Override]
    public function deleteMultiple(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    #[Override]
    public function has(string $key): bool
    {
        $key = $this->getCacheKey($key);
        return Arr::has($this->array, $key);
    }

    #[Override]
    public function clear(): bool
    {
        $this->array = [];
        return true;
    }
}
