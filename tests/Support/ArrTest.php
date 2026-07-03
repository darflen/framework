<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Support;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Support\Arr;

class ArrTest extends TestCase
{
    private array $array = ['foo' => 'bar', 'fizzbuzz' => ['fizz', 'buzz', 'bazz'], 'baz' => ['hello' => 'world']];

    public function testDotAndUndot()
    {
        $this->assertSame($this->array, Arr::undot(Arr::dot($this->array)));
    }

    public function testGet()
    {
        $this->assertSame('bar', Arr::get($this->array, 'foo'));
        $this->assertSame('fizz', Arr::get($this->array, 'fizzbuzz.0'));
        $this->assertSame('world', Arr::get($this->array, 'baz.hello'));
        $this->assertSame('success', Arr::get($this->array, 'baz.foo', 'success'));
        $this->assertNotSame('failure', Arr::get($this->array, 'baz.hello', 'failure'));
    }

    public function testSet()
    {
        $input = ['fizz' => 'bazz'];

        Arr::set($input, 'fizz', 'buzz');

        $this->assertSame('buzz', Arr::get($input, 'fizz', 'buzz'));
        $this->assertNotSame('bazz', Arr::get($input, 'fizz', 'bazz'));
    }

    public function testHas()
    {
        $this->assertTrue(Arr::has($this->array, 'foo'));
        $this->assertFalse(Arr::has($this->array, 'bar'));
    }

    public function testMissing()
    {
        $this->assertFalse(Arr::missing($this->array, 'foo'));
        $this->assertTrue(Arr::missing($this->array, 'bar'));
    }

    public function testRemove()
    {
        $input = ['fizz' => 'bazz'];

        Arr::remove($input, 'fizz');

        $this->assertSame('success', Arr::get($input, 'fizz', 'success'));
        $this->assertNotSame('bazz', Arr::get($input, 'fizz', 'success'));
    }

    public function testCartesian()
    {
        $input = [
            ['foo', 'bar'],
            ['fizz', 'buzz']
        ];
        $output = [
            ['foo', 'fizz'],
            ['foo', 'buzz'],
            ['bar', 'fizz'],
            ['bar', 'buzz']
        ];

        $results = Arr::cartesian($input);

        foreach ($results as $index => $result) {
            $this->assertSame($output[$index], $result);
        }
    }
}
