<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Cache;

use Darflen\Framework\Cache\Cache;
use Darflen\Framework\Cache\Drivers\RedisCacheDriver;
use Darflen\Framework\Cache\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Redis;
use Override;

class CacheFeatureTest extends TestCase
{
    private static Cache $cache;
    private static Redis $redis;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$redis = new Redis();
        self::$redis->connect('127.0.0.1');
        self::$redis->setOption(Redis::OPT_PREFIX, 'PHPUNIT_TEST:');
        self::$redis->setOption(Redis::OPT_SCAN, Redis::SCAN_PREFIX);
        self::$cache = new Cache(new RedisCacheDriver(self::$redis));
    }

    public function testOperations(): void
    {
        self::$cache->setMultiple([
            'foo' => 'bar',
            'fizz' => 'buzz',
            'bazz' => 'fazz'
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

    #[Override]
    public function tearDown(): void
    {
        parent::tearDownAfterClass();
        self::$redis->unlink('fizz');
        self::$redis->unlink('buzz');
        self::$redis->unlink('bazz');
        self::$redis->unlink('fazz');
        self::$redis->unlink('foo');
        self::$redis->unlink('bar');
        self::$redis->unlink('baz');
    }
}
