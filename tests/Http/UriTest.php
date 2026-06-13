<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Uri;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Http\Uri;

class UriTest extends TestCase
{
    public function testGetters()
    {
        $uri = new Uri('abcd://username:password@example.com:123/path/data?key=value#fragment%26');

        $this->assertSame('abcd', $uri->getScheme());
        $this->assertSame('username:password', $uri->getUserInfo());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertSame(123, $uri->getPort());
        $this->assertSame('/path/data', $uri->getPath());
        $this->assertSame('key=value', $uri->getQuery());
        $this->assertSame('fragment%26', $uri->getFragment());
        $this->assertSame('username:password@example.com:123', $uri->getAuthority());
    }

    public function testEmptyGetters()
    {
        $uri = new Uri('');

        $this->assertEmpty($uri->getScheme());
        $this->assertEmpty($uri->getUserInfo());
        $this->assertEmpty($uri->getHost());
        $this->assertEmpty($uri->getPort());
        $this->assertEmpty($uri->getPath());
        $this->assertEmpty($uri->getQuery());
        $this->assertEmpty($uri->getFragment());
        $this->assertEmpty($uri->getAuthority());
    }

    public function testWithSetters()
    {
        $uri = new Uri('');

        $clone = $uri->withScheme('https')
            ->withHost('example.com')
            ->withUserInfo('buzz', 'hunter12')
            ->withPort(8080)
            ->withPath("/fizz/buzz/data")
            ->withQuery('key=value')
            ->withFragment('%3Dfoobar%3D');

        $this->assertSame('https', $clone->getScheme());
        $this->assertSame('example.com', $clone->getHost());
        $this->assertSame('buzz:hunter12', $clone->getUserInfo());
        $this->assertSame(8080, $clone->getPort());
        $this->assertSame('/fizz/buzz/data', $clone->getPath());
        $this->assertSame('key=value', $clone->getQuery());
        $this->assertSame('%3Dfoobar%3D', $clone->getFragment());
    }

    public function testToString()
    {
        $uri = new Uri('abcd://username:password@example.com:123/path/data?key=value#fragment%26');

        $this->assertSame('abcd://username:password@example.com:123/path/data?key=value#fragment%26', (string) $uri);
    }
}
