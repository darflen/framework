<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\Factory\CookieFactory;
use Darflen\Framework\Http\Cookie;
use Override;
use PHPUnit\Framework\TestCase;

class CookieTest extends TestCase
{
    private Cookie $cookie;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $cookieFactory = new CookieFactory();
        $this->cookie = $cookieFactory->createCookie('name', 'value', 1000, '/', 'example.com', true, true, 'None');
    }

    public function testGetters(): void
    {
        $this->assertSame('name', $this->cookie->getName());
        $this->assertSame('value', $this->cookie->getValue());
        $this->assertSame(1000, $this->cookie->getExpiration());
        $this->assertSame('/', $this->cookie->getPath());
        $this->assertSame('example.com', $this->cookie->getDomain());
        $this->assertSame(true, $this->cookie->isSecure());
        $this->assertSame(true, $this->cookie->isHttpOnly());
        $this->assertSame('None', $this->cookie->getSameSite());
    }

    public function testSetters(): void
    {
        $this->cookie = $this->cookie->withDomain('fizzbuzz.com')
            ->withSameSite('Lax')
            ->withSecure(false)
            ->withHttpOnly(false)
            ->withPath('/fizz/buzz')
            ->withExpiration(2000)
            ->withName('foo')
            ->withValue('bar');

        $this->assertSame('foo', $this->cookie->getName());
        $this->assertSame('bar', $this->cookie->getValue());
        $this->assertSame(2000, $this->cookie->getExpiration());
        $this->assertSame('/fizz/buzz', $this->cookie->getPath());
        $this->assertSame('fizzbuzz.com', $this->cookie->getDomain());
        $this->assertSame(false, $this->cookie->isSecure());
        $this->assertSame(false, $this->cookie->isHttpOnly());
        $this->assertSame('Lax', $this->cookie->getSameSite());
    }
}
