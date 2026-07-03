<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\Factory\ServerRequestFactory;
use Darflen\Framework\Http\RequestContext;
use Override;
use PHPUnit\Framework\TestCase;

class RequestContextFeatureTest extends TestCase
{
    private RequestContext $requestContext;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $serverRequest = new ServerRequestFactory();
        $serverRequest = $serverRequest->createServerRequest('GET', 'https://example.com/search?q=fizzbuzz&o=trending', $_SERVER);
        $serverRequest = $serverRequest->withHeader('Origin', 'https://foobar.com');
        $serverRequest = $serverRequest->withHeader('Authorization', 'Bearer foobar');
        $serverRequest = $serverRequest->withHeader('Accept', ['application/json', 'text/csv;q=0.1', 'text/markdown;q=0.9']);
        $serverRequest = $serverRequest->withHeader('CF-CONNECTING-IP', '196.168.1.1');
        $serverRequest = $serverRequest->withHeader('X-FORWARDED-FOR', ['196.168.1.1']);
        $serverRequest = $serverRequest->withParsedBody(['foo' => 'bar', 'fizz' => '']);
        $this->requestContext = new RequestContext($serverRequest);
    }

    public function testGetPath(): void
    {
        $this->assertSame('/search', $this->requestContext->getPath());
    }

    public function testGetUrl(): void
    {
        $this->assertSame('https://example.com/search', $this->requestContext->getUrl());
    }

    public function testGetFullUrl(): void
    {
        $this->assertSame('https://example.com/search?q=fizzbuzz&o=trending', $this->requestContext->getFullUrl());
    }

    public function testGetFullUrlWithQuery(): void
    {
        $this->assertSame('https://example.com/search?q=fizzbuzz&o=trending&fizz=buzz&foo=bar', $this->requestContext->getFullUrlWithQuery(['fizz' => 'buzz', 'foo' => 'bar']));
    }

    public function testGetFullUrlWithoutQuery(): void
    {
        $this->assertSame('https://example.com/search', $this->requestContext->getFullUrlWithoutQuery(['q', 'o']));
        $this->assertSame('https://example.com/search?o=trending', $this->requestContext->getFullUrlWithoutQuery(['q']));
    }

    public function testGetHostAndHttpHost(): void
    {
        $this->assertSame('example.com', $this->requestContext->getHost());
        $this->assertSame('example.com', $this->requestContext->getHttpHost());
    }

    public function testGetOrigin(): void
    {
        $this->assertSame('https://foobar.com', $this->requestContext->getOrigin());
    }

    public function testGetSchemeAndHttpHost(): void
    {
        $this->assertSame('https://example.com', $this->requestContext->getSchemeAndHttpHost());
    }

    public function testGetAuthorization(): void
    {
        $this->assertSame('Bearer foobar', $this->requestContext->getAuthorization());
    }

    public function testGetBearerToken(): void
    {
        $this->assertSame('foobar', $this->requestContext->getBearerToken());
    }

    public function testGetIp(): void
    {
        $this->assertSame('196.168.1.1', $this->requestContext->getIp());
    }

    public function testGetIps(): void
    {
        $this->assertSame(['196.168.1.1'], $this->requestContext->getIps());
    }

    public function testGetAcceptableContentTypes(): void
    {
        $this->assertSame('application/json,text/csv,text/markdown', implode(',', $this->requestContext->getAcceptableContentTypes()));
    }

    public function testIsAcceptableTypes(): void
    {
        $this->assertTrue($this->requestContext->isAcceptableTypes(['text/csv', 'application/json']));
        $this->assertTrue($this->requestContext->isAcceptableTypes(['text/markdown']));
        $this->assertFalse($this->requestContext->isAcceptableTypes(['text/plain', 'image/jpg']));
        $this->assertFalse($this->requestContext->isAcceptableTypes(['text/plain', 'application/json']));
    }

    public function testGetPreferedType(): void
    {
        $this->assertSame('application/json', $this->requestContext->getPreferedType(['text/csv', 'application/json']));
        $this->assertSame('application/json', $this->requestContext->getPreferedType(['application/json', 'text/markdown']));
        $this->assertSame('text/markdown', $this->requestContext->getPreferedType(['text/markdown', 'text/csv']));
    }

    public function testGetAll(): void
    {
        $this->assertSame(['q' => 'fizzbuzz', 'o' => 'trending', 'foo' => 'bar', 'fizz' => ''], $this->requestContext->getAll());
    }

    public function testGetInput(): void
    {
        $this->assertSame('fizzbuzz', $this->requestContext->getInput('q', 'failure'));
        $this->assertSame('success', $this->requestContext->getInput('f', 'success'));
        $this->assertSame('bar', $this->requestContext->getInput('foo', 'failure'));

    }

    public function testHasInput(): void
    {
        $this->assertTrue($this->requestContext->hasInput('q'));
        $this->assertFalse($this->requestContext->hasInput('f'));
    }

    public function testHasAnyInput(): void
    {
        $this->assertTrue($this->requestContext->hasAnyInput(['q', 'f', 'b']));
        $this->assertTrue($this->requestContext->hasAnyInput(['o']));
        $this->assertFalse($this->requestContext->hasAnyInput(['p']));
        $this->assertFalse($this->requestContext->hasAnyInput(['f', 'z', 'k']));
    }

    public function testGetQuery(): void
    {
        $this->assertSame('fizzbuzz', $this->requestContext->getQuery('q', 'failure'));
        $this->assertSame('success', $this->requestContext->getQuery('f', 'success'));
        $this->assertSame('success', $this->requestContext->getQuery('foo', 'success'));
    }

    public function testIsMethod(): void
    {
        $this->assertTrue($this->requestContext->isMethod('GET'));
        $this->assertFalse($this->requestContext->isMethod('POST'));
    }

    public function testIsFilled(): void
    {
        $this->assertTrue($this->requestContext->isFilled('q'));
        $this->assertTrue($this->requestContext->isFilled('foo'));
        $this->assertFalse($this->requestContext->isFilled('buzz'));
    }

    public function testIsNotFilled(): void
    {
        $this->assertFalse($this->requestContext->isNotFilled('q'));
        $this->assertFalse($this->requestContext->isNotFilled('foo'));
        $this->assertTrue($this->requestContext->isNotFilled('buzz'));
    }

    public function testIsMissing(): void
    {
        $this->assertFalse($this->requestContext->isMissing('q'));
        $this->assertFalse($this->requestContext->isMissing('foo'));
        $this->assertTrue($this->requestContext->isMissing('buzz'));
    }
}
