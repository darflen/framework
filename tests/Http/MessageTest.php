<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\Stream;
use Darflen\Framework\Http\Message;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class MessageTest extends TestCase
{
    public function testGetProtocolVersion()
    {
        $message = new Message();

        $this->assertEquals('1.1', $message->getProtocolVersion());
    }

    public function testWithProtocolVersion()
    {
        $message = new Message();

        $clone = $message->withProtocolVersion('2.0');

        $this->assertEquals('2.0', $clone->getProtocolVersion());
        $this->assertEquals('1.1', $message->getProtocolVersion());
    }

    public function testWithProtocolVersionThrowsExceptionWhenBadValue()
    {
        $this->expectException(InvalidArgumentException::class);

        $message = new Message();

        $message->withProtocolVersion('0.0');
    }

    public function testGetHeaders()
    {
        $message = new Message();

        $clone = $message->withHeader('X-foo', 'foo');
        $clone = $clone->withHeader('X-bar', 'bar');
        $clone = $clone->withAddedHeader('X-bar', 'bar 2');

        $expected = [
            'X-foo' => ['foo'],
            'X-bar' => ['bar', 'bar 2']
        ];

        $this->assertSame($expected, $clone->getHeaders());

    }

    public function testGetHeader()
    {
        $message = new Message();

        $clone = $message->withHeader('X-foo', 'foo');
        $clone = $clone->withHeader('X-bar', 'bar');

        $this->assertEquals(['foo'], $clone->getHeader('X-foo'));
        $this->assertEquals(['bar'], $clone->getHeader('X-bar'));
        $this->assertEquals([], $clone->getHeader('X-fizz'));
    }

    public function testGetHeaderLine()
    {
        $message = new Message();

        $clone = $message->withHeader('X-foo', 'foo');
        $clone = $clone->withHeader('X-bar', 'bar');

        $this->assertEquals('foo', $clone->getHeaderLine('X-foo'));
        $this->assertEquals('bar', $clone->getHeaderLine('X-bar'));
        $this->assertEquals('', $clone->getHeaderLine('X-fizz'));
    }

    public function testHasHeader()
    {
        $message = new Message();

        $clone = $message->withHeader('X-foo', 'foo');

        $this->assertTrue($clone->hasHeader('X-foo'));
        $this->assertFalse($clone->hasHeader('X-bar'));
    }

    public function testWithHeader()
    {
        $message = new Message();

        $clone = $message->withHeader('X-foo', 'failure');
        $clone = $clone->withHeader('X-foo', 'bar');
        $clone = $clone->withHeader('X-bar', ['fizz', 'buzz']);

        $this->assertEquals('bar', $clone->getHeaderLine('X-foo'));
        $this->assertEquals('fizz,buzz', $clone->getHeaderLine('X-bar'));
    }

    public function testWithoutHeader()
    {
        $message = new Message();

        $clone = $message->withHeader('X-foo', 'failure');
        $clone = $clone->withoutHeader('X-foo');

        $this->assertEquals('', $clone->getHeaderLine('X-foo'));
    }

    public function testWithHeaderNameThrowsExceptionWhenBadName()
    {
        $this->expectException(InvalidArgumentException::class);

        $message = new Message();

        $message->withHeader("x-foo\x0A", 'failure');
    }

    public function testWithHeaderValueThrowsExceptionWhenBadValue()
    {
        $this->expectException(InvalidArgumentException::class);

        $message = new Message();

        $message->withHeader('x-foo', "failure\x0A");
    }

    public function testWithAddedHeader()
    {
        $message = new Message();

        $clone = $message->withAddedHeader('X-foo', 'foo');
        $clone = $clone->withAddedHeader('X-foo', 'bar');
        $clone = $clone->withAddedHeader('X-bar', ['fizz', 'foo']);
        $clone = $clone->withAddedHeader('X-bar', ['bar', 'buzz']);

        $this->assertEquals('foo,bar', $clone->getHeaderLine('X-foo'));
        $this->assertEquals('fizz,foo,bar,buzz', $clone->getHeaderLine('X-bar'));
    }

    public function testWithAddedHeaderNameThrowsExceptionWhenBadName()
    {
        $this->expectException(InvalidArgumentException::class);

        $message = new Message();

        $message->withAddedHeader("x-foo\x0A", 'bar');
        $message->withAddedHeader("x-foo\x0A", 'fizz');
    }

    public function testWithAddedHeaderValueThrowsExceptionWhenBadValue()
    {
        $this->expectException(InvalidArgumentException::class);

        $message = new Message();

        $message->withAddedHeader('x-foo', "bar");
        $message->withAddedHeader('x-foo', "fizz\x0A");
    }

    public function testWithBody()
    {
        $message = new Message();

        $stream = $this->createStub(Stream::class);

        $clone = $message->withBody($stream);

        $this->assertNotSame($clone, $message);
        $this->assertSame($stream, $clone->getBody());
    }
}
