<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Cache;

use Darflen\Framework\Cache\Cache;
use Darflen\Framework\Cache\Drivers\ArrayCacheDriver;
use Darflen\Framework\Cache\Exceptions\InvalidArgumentException;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\TestCase;

class CacheFeatureTest extends TestCase
{
    private static Cache $cache;
    private static array $array = [];

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$cache = new Cache(new ArrayCacheDriver(self::$array, new DateTimeImmutable()));
    }

    public function testOperations(): void
    {
        self::$cache->setMultiple([
            'foo' => 'bar',
            'fizz' => 'buzz',
            'bazz' => 'fazz',
        ]);
        self::$cache->set('bar', 'baz');
        $results = self::$cache->getMultiple(['foo', 'bazz']);
        self::$cache->delete('fizz');
        self::$cache->deleteMultiple(['bar', 'bazz']);
        $this->assertTrue(self::$cache->has('foo'));
        $this->assertFalse(self::$cache->has('bazz'));
        $this->assertFalse(self::$cache->has('fizz'));
        $this->assertFalse(self::$cache->has('bar'));
        $this->assertSame('fazz', $results['bazz']);
        $this->assertSame('bar', $results['foo']);
        $this->assertSame('bar', self::$cache->get('foo', 'failure'));
        $this->assertSame('success', self::$cache->get('bazz', 'success'));
    }

    public function testClear(): void
    {
        self::$cache->set('foo', 'bar');
        self::$cache->clear();
        $this->assertFalse(self::$cache->has('foo'));
    }

    public function testKeyValidationThrowsExceptionWhenInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        self::$cache->set('{}|value', 'failure');
    }
}
