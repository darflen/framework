<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\Cookie;
use Darflen\Framework\Http\CookieJar;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CookieJarTest extends TestCase
{
    public function testCookieJarConstruct(): void
    {
        $cookieMock1 = $this->createMock(Cookie::class);
        $cookieMock2 = $this->createMock(Cookie::class);
        $cookieMock3 = $this->createMock(Cookie::class);
        $cookieMock1->expects($this->once())->method('getName')->willReturn('fizz');
        $cookieMock2->expects($this->once())->method('getName')->willReturn('buzz');
        $cookieMock3->expects($this->once())->method('getName')->willReturn('bazz');

        $cookieJar = new CookieJar([
            $cookieMock1,
            $cookieMock2,
            $cookieMock3
        ]);

        $this->assertSame([
            'fizz' => $cookieMock1,
            'buzz' => $cookieMock2,
            'bazz' => $cookieMock3
        ], $cookieJar->getAll());
    }

    public function testBadCookieThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CookieJar(['fizzbuzz']);
    }

    public function testCookieJarGetters(): void
    {
        $cookieMock = $this->createMock(Cookie::class);
        $cookieMock->expects($this->once())->method('getName')->willReturn('foo');
        $cookieMock->expects($this->once())->method('getValue')->willReturn('bar');

        $cookieJar = new CookieJar([$cookieMock]);

        $this->assertSame('bar', $cookieJar->getCookieValue('foo', 'failure'));
        $this->assertSame('success', $cookieJar->getCookieValue('nothing', 'success'));
        $this->assertSame($cookieMock, $cookieJar->getCookie('foo'));
        $this->assertSame(['foo' => $cookieMock], $cookieJar->getAll());
        $this->assertSame(1, $cookieJar->getCount());
        $this->assertSame(true, $cookieJar->hasCookie('foo'));
        $this->assertSame(false, $cookieJar->hasCookie('bar'));
    }

    public function testWithCookie(): void
    {
        $cookieMock1 = $this->createMock(Cookie::class);
        $cookieMock2 = $this->createMock(Cookie::class);
        $cookieMock1->expects($this->once())->method('getName')->willReturn('fizz');
        $cookieMock2->expects($this->once())->method('getName')->willReturn('foo');


        $cookieJar = new CookieJar([$cookieMock1]);
        $cookieJar = $cookieJar->withCookie($cookieMock2);

        $this->assertSame(['fizz' => $cookieMock1, 'foo' => $cookieMock2], $cookieJar->getAll());
    }

    public function testWithCookies(): void
    {
        $cookieMock1 = $this->createMock(Cookie::class);
        $cookieMock2 = $this->createMock(Cookie::class);
        $cookieMock3 = $this->createMock(Cookie::class);
        $cookieMock1->expects($this->once())->method('getName')->willReturn('fizz');
        $cookieMock2->expects($this->once())->method('getName')->willReturn('buzz');
        $cookieMock3->expects($this->once())->method('getName')->willReturn('bazz');

        $cookieJar = new CookieJar([$cookieMock1]);
        $cookieJar = $cookieJar->withCookies([$cookieMock2, $cookieMock3]);

        $this->assertSame(['fizz' => $cookieMock1, 'buzz' => $cookieMock2, 'bazz' => $cookieMock3], $cookieJar->getAll());
    }

    public function testWithoutCookie(): void
    {
        $cookieMock1 = $this->createMock(Cookie::class);
        $cookieMock2 = $this->createMock(Cookie::class);
        $cookieMock1->expects($this->once())->method('getName')->willReturn('fizz');
        $cookieMock2->expects($this->once())->method('getName')->willReturn('foo');


        $cookieJar = new CookieJar([$cookieMock1, $cookieMock2]);
        $cookieJar = $cookieJar->withoutCookie('fizz');

        $this->assertSame(['foo' => $cookieMock2], $cookieJar->getAll());
    }

    public function testClearAll(): void
    {
        $cookieMock1 = $this->createMock(Cookie::class);
        $cookieMock2 = $this->createMock(Cookie::class);
        $cookieMock1->expects($this->once())->method('getName')->willReturn('fizz');
        $cookieMock2->expects($this->once())->method('getName')->willReturn('foo');


        $cookieJar = new CookieJar([$cookieMock1, $cookieMock2]);
        $cookieJar = $cookieJar->clearAll();

        $this->assertSame([], $cookieJar->getAll());
    }
}
