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

        $this->assertSame('fizz', env('EXAMPLE_BAR', 'failure'));
        $this->assertSame('buzz', $config->get('config.example.fizz', 'failure'));
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
