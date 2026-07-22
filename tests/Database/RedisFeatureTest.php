<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Database;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Database\Redis;
use PHPUnit\Framework\TestCase;
use Redis as GlobalRedis;

class RedisFeatureTest extends TestCase
{
    private static Config $config;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $config = new Config();
        $config->loadConfigArray('database', [
            'redis' => [
                'host' => '127.0.0.1',
                'port' => 6379,
                'database' => 0,
                'username' => '',
                'password' => '',
                'read_write_timeout' => 30,
                'persistent' => false,
                'persistent_id' => 'default',
                'options' => [],
            ],
        ]);
        self::$config = $config;
    }

    public function testGetInstance(): void
    {
        $redis = new Redis(self::$config);

        $this->assertInstanceOf(GlobalRedis::class, $redis->getInstance());
    }
}
