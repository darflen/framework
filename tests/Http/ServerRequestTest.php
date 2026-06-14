<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\ServerRequest;
use Darflen\Framework\Http\UploadedFile;
use Darflen\Framework\Http\Uri;
use Darflen\Framework\Http\Stream;
use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    public function testEmptyGetters()
    {
        $uri = $this->createStub(Uri::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $this->assertEmpty($serverRequest->getAttributes());
        $this->assertEmpty($serverRequest->getQueryParams());
        $this->assertEmpty($serverRequest->getServerParams());
        $this->assertEmpty($serverRequest->getCookieParams());
        $this->assertEmpty($serverRequest->getUploadedFiles());
    }

    public function testWithSetters()
    {
        $uri = $this->createStub(Uri::class);
        $body = $this->createStub(Stream::class);
        $uploadedFile = $this->createStub(UploadedFile::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $clone = $serverRequest->withCookieParams(['foo' => 'bar'])
            ->withQueryParams(['fizz' => 'buzz'])
            ->withParsedBody($body)
            ->withUploadedFiles([$uploadedFile]);

        $this->assertArraysAreIdentical(['foo' => 'bar'], $clone->getCookieParams());
        $this->assertArraysAreIdentical(['fizz' => 'buzz'], $clone->getQueryParams());
        $this->assertArraysAreIdentical([$uploadedFile], $clone->getUploadedFiles());
        $this->assertSame($body, $clone->getParsedBody());
    }

    public function testWithAttribute()
    {
        $uri = $this->createStub(Uri::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $clone = $serverRequest->withAttribute('foo', 'bar');

        $this->assertSame('bar', $clone->getAttribute('foo', 'failure'));
        $this->assertSame('success', $clone->getAttribute('failure', 'success'));
    }

    public function testWithoutAttribute()
    {
        $uri = $this->createStub(Uri::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $clone = $serverRequest->withAttribute('fizz', 'buzz');
        $clone = $serverRequest->withoutAttribute('fizz');

        $this->assertSame('success', $clone->getAttribute('fizz', 'success'));
        $this->assertNotSame('buzz', $clone->getAttribute('fizz', 'failure'));
    }

    public function testGetAttributes()
    {
        $uri = $this->createStub(Uri::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $clone = $serverRequest->withAttribute('foo', 'bar')
            ->withAttribute('foo', 'success')
            ->withAttribute('fizz', 'buzz');

        $this->assertArraysAreIdentical(['foo' => 'success', 'fizz' => 'buzz'], $clone->getAttributes());
    }
}
