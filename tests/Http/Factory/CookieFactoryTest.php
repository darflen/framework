<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http\Factory;

use Darflen\Framework\Http\Factory\CookieFactory;
use PHPUnit\Framework\TestCase;

class CookieFactoryTest extends TestCase
{
    public function testCreateCookie(): void
    {
        $cookieFactory = new CookieFactory();
        $cookie = $cookieFactory->createCookie('name', 'value', 1000, '/', 'example.com', true, true, 'None');

        $this->assertSame('name=value; Expires=Thu, 01 Jan 1970 00:16:40 GMT; Path=/; Domain=example.com; Secure; HttpOnly; SameSite=None', (string) $cookie);
    }

    public function testCreatePermanentCookie(): void
    {
        $cookieFactory = new CookieFactory();
        $cookie = $cookieFactory->createPermanentCookie('name', 'value', '/', 'example.com', true, true, 'None');
        $this->assertGreaterThan(time(), $cookie->getExpiration());
    }

    public function testExpireCookie(): void
    {
        $cookieFactory = new CookieFactory();
        $cookie = $cookieFactory->expireCookie('name', '/', 'example.com');
        $this->assertLessThan(time(), $cookie->getExpiration());
    }
}
