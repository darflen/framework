<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\ServerRequest;
use Darflen\Framework\Http\UploadedFile;
use Darflen\Framework\Http\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    public function testEmptyGetters(): void
    {
        $uri = $this->createStub(Uri::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $this->assertEmpty($serverRequest->getAttributes());
        $this->assertEmpty($serverRequest->getQueryParams());
        $this->assertEmpty($serverRequest->getServerParams());
        $this->assertEmpty($serverRequest->getCookieParams());
        $this->assertEmpty($serverRequest->getUploadedFiles());
    }

    public function testWithSetters(): void
    {
        $uri = $this->createStub(Uri::class);
        $uploadedFile = $this->createStub(UploadedFile::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $clone = $serverRequest->withCookieParams(['foo' => 'bar'])
            ->withQueryParams(['fizz' => 'buzz'])
            ->withParsedBody(['foo' => 'bar'])
            ->withUploadedFiles([$uploadedFile]);

        $this->assertArraysAreIdentical(['foo' => 'bar'], $clone->getCookieParams());
        $this->assertArraysAreIdentical(['fizz' => 'buzz'], $clone->getQueryParams());
        $this->assertArraysAreIdentical([$uploadedFile], $clone->getUploadedFiles());
        $this->assertSame(['foo' => 'bar'], $clone->getParsedBody());
    }

    public function testWithAttribute(): void
    {
        $uri = $this->createStub(Uri::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $clone = $serverRequest->withAttribute('foo', 'bar');

        $this->assertSame('bar', $clone->getAttribute('foo', 'failure'));
        $this->assertSame('success', $clone->getAttribute('failure', 'success'));
    }

    public function testWithoutAttribute(): void
    {
        $uri = $this->createStub(Uri::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $clone = $serverRequest->withAttribute('fizz', 'buzz');
        $clone = $serverRequest->withoutAttribute('fizz');

        $this->assertSame('success', $clone->getAttribute('fizz', 'success'));
        $this->assertNotSame('buzz', $clone->getAttribute('fizz', 'failure'));
    }

    public function testGetAttributes(): void
    {
        $uri = $this->createStub(Uri::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $clone = $serverRequest->withAttribute('foo', 'bar')
            ->withAttribute('foo', 'success')
            ->withAttribute('fizz', 'buzz');

        $this->assertArraysAreIdentical(['foo' => 'success', 'fizz' => 'buzz'], $clone->getAttributes());
    }

    public function testWithUploadedFilesWithBadTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $uri = $this->createStub(Uri::class);
        $serverRequest = new ServerRequest('GET', $uri);

        $serverRequest->withUploadedFiles(['']);
    }
}
