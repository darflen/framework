<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\Request;
use Darflen\Framework\Http\Uri;
use Darflen\Framework\Http\Stream;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class RequestTest extends TestCase
{
    public function testGettersWithStubs()
    {
        $uri = $this->createStub(Uri::class);
        $body = $this->createStub(Stream::class);

        $request = new Request("POST", $uri, [], $body);

        $this->assertSame('POST', $request->getMethod());
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertSame('/', $request->getRequestTarget());
    }

    public function testWithMethod()
    {
        $uri = $this->createStub(Uri::class);
        $body = $this->createStub(Stream::class);

        $request = new Request('GET', $uri, [], $body);
        $clone = $request->withMethod("POST");

        $this->assertSame('POST', $clone->getMethod());
        $this->assertSame('GET', $request->getMethod());
    }

    public function testGetRequestTarget()
    {
        $uri = $this->createMock(Uri::class);
        $body = $this->createStub(Stream::class);
        $uri->expects($this->once())->method('getPath')->willReturn('/fizz/buzz');
        $uri->expects($this->once())->method('getQuery')->willReturn('key=value');

        $request = new Request('GET', $uri, [], $body);

        $this->assertSame('/fizz/buzz?key=value', $request->getRequestTarget());
    }

    public function testWithRequestTarget()
    {
        $uri = $this->createStub(Uri::class);
        $body = $this->createStub(Stream::class);

        $request = new Request('GET', $uri, [], $body);
        $clone = $request->withRequestTarget('https://example.com/data/');

        $this->assertSame('https://example.com/data/', $clone->getRequestTarget());
    }

    public function testWithUri()
    {
        $uri = $this->createMock(Uri::class);
        $body = $this->createStub(Stream::class);
        $uri->expects($this->exactly(5))->method('getHost')->willReturnOnConsecutiveCalls('example.com', 'example.com', 'example.com', 'example.com', 'fizzbuzz.com');

        $request = new Request('GET', $uri, [], $body);
        $clone = $request->withUri($uri);

        $this->assertSame('example.com', $request->getUri()->getHost());
        $this->assertSame('fizzbuzz.com', $clone->getUri()->getHost());
    }
}
