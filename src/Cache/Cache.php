<?php

declare(strict_types=1);

namespace Darflen\Framework\Cache;

use Darflen\Framework\Cache\Exceptions\InvalidArgumentException;
use Override;
use Psr\SimpleCache\CacheInterface;
use Darflen\Framework\Cache\Drivers\CacheDriverInterface;
use DateInterval;

class Cache implements CacheInterface
{
    private CacheDriverInterface $strategy;

    public function __construct(CacheDriverInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    protected function validateKey(string $key): void
    {
        if (preg_match('/[{}()\/\\@]/', $key)) {
            throw new InvalidArgumentException("Key contains reserved characters");
        }
    }

    protected function validateKeys(array $keys): void
    {
        foreach ($keys as $key) {
            $this->validateKey($key);
        }
    }

    #[Override]
    public function get(string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);
        return $this->strategy->get($key, $default);
    }

    #[Override]
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $this->validateKeys(array_values($keys));
        return $this->strategy->getMultiple($keys, $default);
    }

    #[Override]
    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $this->validateKey($key);
        return $this->strategy->set($key, $value, $ttl);
    }

    #[Override]
    public function setMultiple(iterable $values, null|int|DateInterval $ttl = null): bool
    {
        $this->validateKeys(array_values($values));
        return $this->strategy->setMultiple($values, $ttl);
    }

    #[Override]
    public function delete(string $key): bool
    {
        $this->validateKey($key);
        return $this->strategy->delete($key);
    }

    #[Override]
    public function deleteMultiple(iterable $keys): bool
    {
        $this->validateKeys(array_values($keys));
        return $this->strategy->deleteMultiple($keys);
    }

    #[Override]
    public function has(string $key): bool
    {
        $this->validateKey($key);
        return $this->strategy->has($key);
    }

    #[Override]
    public function clear(): bool
    {
        return $this->strategy->clear();
    }
}
