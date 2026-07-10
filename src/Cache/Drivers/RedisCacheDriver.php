<?php

declare(strict_types=1);

namespace Darflen\Framework\Cache\Drivers;

use Override;
use Redis;
use DateInterval;

class RedisCacheDriver implements CacheDriverInterface
{
    private Redis $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    protected function parseTTL(null|int|DateInterval $ttl): ?int
    {
        if ($ttl instanceof DateInterval) {
            $ttl = date_create('@0')->add($ttl)->getTimestamp();
        }
        return $ttl;
    }

    #[Override]
    public function get(string $key, mixed $default = null): mixed
    {
        $result = $this->redis->get($key);
        return $result ? @unserialize($result) : $default;
    }

    #[Override]
    public function getMultiple(array $keys, mixed $default = null): array
    {
        $results = $this->redis->mGet($keys);
        if (!$results) {
            return $default;
        }
        foreach ($results as $index => $result) {
            $results[$index] = @unserialize($result);
        }
        return array_combine($keys, $results);
    }

    #[Override]
    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $ttl = $this->parseTTL($ttl);
        if ($ttl && $ttl <= 0) {
            return $this->delete($key);
        }
        if ($ttl) {
            $ttl = ['ex' => $ttl];
        }
        return $this->redis->set($key, @serialize($value), $ttl);
    }

    #[Override]
    public function setMultiple(array $values, null|int|DateInterval $ttl = null): bool
    {
        $ttl = $this->parseTTL($ttl);
        if ($ttl === 0) {
            $ttl = -1;
        }
        foreach ($values as $index => $value) {
            $values[$index] = @serialize($value);
        }
        $tx = $this->redis->multi();
        $tx->mSet($values);
        if ($ttl) {
            foreach ($values as $key => $value) {
                $tx->expire($key, $ttl);
            }
        }
        return $tx->exec() !== false;
    }

    #[Override]
    public function delete(string $key): bool
    {
        return $this->redis->unlink($key) !== false;
    }

    #[Override]
    public function deleteMultiple(array $keys): bool
    {
        return $this->redis->unlink($keys) !== false;
    }

    #[Override]
    public function has(string $key): bool
    {
        return $this->redis->exists($key) > 0;
    }

    #[Override]
    public function clear(): bool
    {
        $iterator = null;
        $allKeys = [];
        do {
            $keys = $this->redis->scan($iterator, "*");
            if (!empty($keys)) {
                foreach ($keys as $key) {
                    $allKeys[] = $key;
                }
            }
        } while ($iterator > 0);
        $prefix = (string) $this->redis->getOption(Redis::OPT_PREFIX);
        $allKeys = array_map(function ($value) use ($prefix) {
            if (str_starts_with($value, $prefix)) {
                $value = substr($value, strlen($prefix));
            }
            return $value;
        }, $allKeys);
        return $this->deleteMultiple($allKeys);
    }
}
