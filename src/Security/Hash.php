<?php

declare(strict_types=1);

namespace Darflen\Framework\Security;

class Hash
{
    protected string $algorithm;
    protected array $parameters = [];

    public function __construct()
    {
        $this->algorithm = config('security.hashing.algorithm');
        $this->parameters = config('security.hashing.' . $this->algorithm);
    }

    public function createHash(string $plain, array $parameters = [])
    {
        $parameters = array_merge($this->parameters, $parameters);
        return password_hash($plain, $this->algorithm, $parameters);
    }

    public function checkHash(string $plain, string $hashed)
    {
        return password_verify($plain, $hashed);
    }

    public function needsRehash(string $hashed, array $parameters = [])
    {
        $parameters = array_merge($this->parameters, $parameters);
        return password_needs_rehash($hashed, $this->algorithm, $parameters);
    }
}
