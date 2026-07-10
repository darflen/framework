<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Config;

use Darflen\Framework\Config\Config;
use Override;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testSetupAndCreate(): void
    {
        $config = new Config();
        $config->loadEnv(__DIR__ . '/Fixtures');
        $config->loadConfigDirectory(__DIR__ . '/Fixtures');
        $config->loadConfigArray('fizzbuzz', ['foobar' => 'bazqux']);

        $this->assertSame('fizz', env('EXAMPLE_BAR', 'failure'));
        $this->assertSame('buzz', $config->get('config.example.fizz', 'failure'));
        $this->assertSame('bazqux', $config->get('fizzbuzz.foobar', 'failure'));
    }

    public function testGet(): void
    {
        $config = new Config();
        $config->loadConfigDirectory(__DIR__ . '/Fixtures');

        $this->assertSame('fizz', $config->get('config.example.bar', 'failure'));
        $this->assertSame('success', $config->get('config.example.nothing', 'success'));
    }

    public function testSet(): void
    {
        $config = new Config();
        $config->loadConfigDirectory(__DIR__ . '/Fixtures');

        $config->set('config.example.boo', 'bar');

        $this->assertSame('bar', $config->get('config.example.boo'));
    }

    public function testRemove(): void
    {
        $config = new Config();
        $config->loadConfigDirectory(__DIR__ . '/Fixtures');

        $config->remove('config.example.fizz');

        $this->assertSame('success', $config->get('config.example.fizz', 'success'));
    }

    public function testAll(): void
    {
        $config = new Config();
        $config->loadConfigDirectory(__DIR__ . '/Fixtures');
        $config->set('config.example.boo', 'bar');

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
        ], $config->all());
    }
}
