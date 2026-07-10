<?php

declare(strict_types=1);

namespace Darflen\Framework\Security;

use Darflen\Framework\Config\Config;

class Hash
{
    protected string $algorithm;
    protected array $parameters = [];

    public function __construct(Config $config)
    {
        $this->algorithm = $config->get('security.hashing.algorithm');
        $this->parameters = $config->get('security.hashing.' . $this->algorithm);
    }

    public function createHash(string $plain, array $parameters = []): string
    {
        $parameters = array_merge($this->parameters, $parameters);
        return password_hash($plain, $this->algorithm, $parameters);
    }

    public function checkHash(string $plain, string $hashed): bool
    {
        return password_verify($plain, $hashed);
    }

    public function needsRehash(string $hashed, array $parameters = []): bool
    {
        $parameters = array_merge($this->parameters, $parameters);
        return password_needs_rehash($hashed, $this->algorithm, $parameters);
    }
}
