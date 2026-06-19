<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Factory;

use Darflen\Framework\Http\Cookie;

class CookieFactory
{
    public function createCookie(string $name, string $value = '', int $expiresAt = 0, string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false, string $sameSite = ''): Cookie
    {
        return new Cookie($name, $value, $expiresAt, $path, $domain, $secure, $httpOnly, $sameSite);
    }

    public function createPermanentCookie(string $name, string $value = '', string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false, string $sameSite = ''): Cookie
    {
        return $this->createCookie($name, $value, time() + 34560000, $path, $domain, $secure, $httpOnly, $sameSite);
    }

    public function expireCookie(string $name, string $path = '', string $domain = ''): Cookie
    {
        return $this->createCookie($name, '', time() - 34560000, $path, $domain);
    }
}
