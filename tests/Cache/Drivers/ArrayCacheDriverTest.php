<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Cache\Drivers;

use Darflen\Framework\Cache\Drivers\ArrayCacheDriver;
use DateInterval;
use DateTimeImmutable;
use Override;
use PHPUnit\Framework\TestCase;
use stdClass;

class ArrayCacheDriverTest extends TestCase
{
    private static ArrayCacheDriver $strategy;
    private static array $array = [];

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$strategy = new ArrayCacheDriver(self::$array, new DateTimeImmutable());
    }

    public function testSetAndGet(): void
    {
        self::$strategy->set('fizz', 'buzz');
        self::$strategy->set('buzz', true);
        self::$strategy->set('bazz', PHP_INT_MAX);
        self::$strategy->set('fazz', new stdClass());
        self::$strategy->set('foo', ['bar', 'baz']);
        self::$strategy->set('bar', 3.14159);
        self::$strategy->set('baz', null);
        $this->assertSame('buzz', self::$strategy->get('fizz'));
        $this->assertSame(true, self::$strategy->get('buzz'));
        $this->assertSame(PHP_INT_MAX, self::$strategy->get('bazz'));
        $this->assertEquals(new stdClass(), self::$strategy->get('fazz'));
        $this->assertSame(['bar', 'baz'], self::$strategy->get('foo'));
        $this->assertSame(3.14159, self::$strategy->get('bar'));
        $this->assertSame(null, self::$strategy->get('baz'));
        $this->assertSame('success', self::$strategy->get('failure', 'success'));
    }

    public function testSetTTL(): void
    {
        self::$strategy->set('fizz', 'buzz', -1);
        self::$strategy->set('foo', 'bar', DateInterval::createFromDateString('1 day'));
        $this->assertSame('success', self::$strategy->get('fizz', 'success'));
        $this->assertSame('bar', self::$strategy->get('foo', 'failure'));
    }

    public function testSetMultipleTTL(): void
    {
        self::$strategy->setMultiple([
            'fizz' => 'buzz',
            'foo' => 'bar',
        ], DateInterval::createFromDateString('1 day'));

        $this->assertSame('bar', self::$strategy->get('foo', 'failure'));
    }

    public function testHas(): void
    {
        self::$strategy->set('foo', 'bar');
        $this->assertTrue(self::$strategy->has('foo'));
        $this->assertFalse(self::$strategy->has('bar'));
    }

    public function testMultipleGetAndSet(): void
    {
        self::$strategy->setMultiple([
            'fizz' => 'buzz',
            'foo' => false,
            'baz' => ['success'],
        ]);
        $results = self::$strategy->getMultiple(['fizz', 'foo', 'baz'], 'failure');
        $this->assertNotSame('failure', $results);
        $this->assertSame('buzz', $results['fizz']);
        $this->assertSame(false, $results['foo']);
        $this->assertSame(['success'], $results['baz']);
    }

    public function testDelete(): void
    {
        self::$strategy->set('foo', 'bar');
        self::$strategy->set('fizz', 'buzz');

        self::$strategy->delete('foo');

        $this->assertFalse(self::$strategy->has('foo'));
        $this->assertTrue(self::$strategy->has('fizz'));
    }

    public function testDeleteMultiple(): void
    {
        self::$strategy->setMultiple([
            'fizz' => 'buzz',
            'foo' => false,
            'baz' => ['success'],
        ]);

        self::$strategy->deleteMultiple(['fizz', 'foo']);

        $this->assertTrue(self::$strategy->has('baz'));
        $this->assertFalse(self::$strategy->has('fizz'));
        $this->assertFalse(self::$strategy->has('foo'));
    }

    public function testClear(): void
    {
        self::$strategy->set('foo', 'bar');
        self::$strategy->set('fizz', 'buzz');

        self::$strategy->clear();

        $this->assertFalse(self::$strategy->has('foo'));
        $this->assertFalse(self::$strategy->has('fizz'));
    }

    #[Override]
    public function tearDown(): void
    {
        parent::tearDownAfterClass();
        self::$array = [];
    }
}
