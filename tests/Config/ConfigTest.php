<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Config;

use Darflen\Framework\Config\Config;
use Override;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private static Config $config;

    public function testSetupAndCreate()
    {
        self::$config = new Config();
        Config::setup(__DIR__ . '/Fixtures', __DIR__ . '/Fixtures')->create();

        $this->assertSame('fizz', env('EXAMPLE_BAR', 'failure'));
        $this->assertSame('buzz', config('config.example.fizz', 'failure'));
    }

    public function testGet()
    {
        self::$config = new Config();
        Config::setup(__DIR__ . '/Fixtures', __DIR__ . '/Fixtures')->create();

        $this->assertSame('fizz', Config::get('config.example.bar', 'failure'));
        $this->assertSame('success', Config::get('config.example.nothing', 'success'));
    }

    public function testSet()
    {
        self::$config = new Config();
        Config::setup(__DIR__ . '/Fixtures', __DIR__ . '/Fixtures')->create();

        Config::set('config.example.boo', 'bar');

        $this->assertSame('bar', config('config.example.boo'));
    }

    public function testAll()
    {
        self::$config = new Config();
        Config::setup(__DIR__ . '/Fixtures', __DIR__ . '/Fixtures')->create();
        Config::set('config.example.boo', 'bar');

        $this->assertSame([
            'config' => [
                'example' => [
                    'key' => 'foo',
                    'foo' => 'bar',
                    'bar' => 'fizz',
                    'fizz' => 'buzz',
                    'variable' => 'fizz',
                    'boo' => 'bar'
                ]
            ]
        ], Config::all());
    }
}
