<?php

declare(strict_types=1);

namespace Darflen\Framework\Database;

use Darflen\Framework\Config\Config;
use Redis as GlobalRedis;

class Redis
{
    private GlobalRedis $redis;

    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getInstance(): GlobalRedis
    {
        if (isset($this->redis)) {
            return $this->redis;
        }
        $redis = new GlobalRedis();
        $redis->connect(
            $this->config->get('database.redis.host'),
            $this->config->get('database.redis.port'),
            $this->config->get('database.redis.read_write_timeout'),
            $this->config->get('database.redis.persistent') ? $this->config->get('database.redis.persistent_id') : null
        );
        $password = $this->config->get('database.redis.username');
        $username = $this->config->get('database.redis.password');
        if ($username !== '' && $password !== '') {
            $redis->auth([$username, $password]);
        } elseif ($password !== '') {
            $redis->auth($password);
        }
        $redis->select($this->config->get('database.redis.database'));
        foreach ($this->config->get('database.redis.options') as $option => $value) {
            $redis->setOption($option, $value);
        }
        $this->redis = $redis;
        return $redis;
    }
}
