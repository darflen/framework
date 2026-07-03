<?php

declare(strict_types=1);

namespace Darflen\Framework\Support;

class Hash
{
    protected static string $algorithm;
    protected static array $parameters = [];

    public function __construct()
    {
        self::$algorithm = config('security.hashing.algorithm');
        self::$parameters = config('security.hashing.' . self::$algorithm);
    }

    public static function make(string $plain, array $parameters = [])
    {
        $parameters = array_merge(self::$parameters, $parameters);
        return password_hash($plain, self::$algorithm, $parameters);
    }

    public static function check(string $plain, string $hashed)
    {
        return password_verify($plain, $hashed);
    }

    public static function needsRehash(string $hashed, array $parameters = [])
    {
        $parameters = array_merge(self::$parameters, $parameters);
        return password_needs_rehash($hashed, self::$algorithm, $parameters);
    }
}
