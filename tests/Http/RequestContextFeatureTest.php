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
        $serverRequest = $serverRequest->withHeader('CF_CONNECTING_IP', '196.168.1.1');
        $serverRequest = $serverRequest->withHeader('X_FORWARDED_FOR', ['196.168.1.1']);
        $serverRequest = $serverRequest->withParsedBody(['foo' => 'bar', 'fizz' => '']);
        $this->requestContext = new RequestContext($serverRequest);
    }

    public function testGetPath()
    {
        $this->assertSame('/search', $this->requestContext->getPath());
    }

    public function testGetUrl()
    {
        $this->assertSame('https://example.com/search', $this->requestContext->getUrl());
    }

    public function testGetFullUrl()
    {
        $this->assertSame('https://example.com/search?q=fizzbuzz&o=trending', $this->requestContext->getFullUrl());
    }

    public function testGetFullUrlWithQuery()
    {
        $this->assertSame('https://example.com/search?q=fizzbuzz&o=trending&fizz=buzz&foo=bar', $this->requestContext->getFullUrlWithQuery(['fizz' => 'buzz', 'foo' => 'bar']));
    }

    public function testGetFullUrlWithoutQuery()
    {
        $this->assertSame('https://example.com/search', $this->requestContext->getFullUrlWithoutQuery(['q', 'o']));
        $this->assertSame('https://example.com/search?o=trending', $this->requestContext->getFullUrlWithoutQuery(['q']));
    }

    public function testGetHostAndHttpHost()
    {
        $this->assertSame('example.com', $this->requestContext->getHost());
        $this->assertSame('example.com', $this->requestContext->getHttpHost());
    }

    public function testGetOrigin()
    {
        $this->assertSame('https://foobar.com', $this->requestContext->getOrigin());
    }

    public function testGetSchemeAndHttpHost()
    {
        $this->assertSame('https://example.com', $this->requestContext->getSchemeAndHttpHost());
    }

    public function testGetAuthorization()
    {
        $this->assertSame('Bearer foobar', $this->requestContext->getAuthorization());
    }

    public function testGetBearerToken()
    {
        $this->assertSame('foobar', $this->requestContext->getBearerToken());
    }

    public function testGetIp()
    {
        $this->assertSame('196.168.1.1', $this->requestContext->getIp());
    }

    public function testGetIps()
    {
        $this->assertSame(['196.168.1.1'], $this->requestContext->getIps());
    }

    public function testGetAcceptableContentTypes()
    {
        $this->assertSame('application/json,text/csv,text/markdown', implode(',', $this->requestContext->getAcceptableContentTypes()));
    }

    public function testIsAcceptableTypes()
    {
        $this->assertTrue($this->requestContext->isAcceptableTypes(['text/csv', 'application/json']));
        $this->assertTrue($this->requestContext->isAcceptableTypes(['text/markdown']));
        $this->assertFalse($this->requestContext->isAcceptableTypes(['text/plain', 'image/jpg']));
        $this->assertFalse($this->requestContext->isAcceptableTypes(['text/plain', 'application/json']));
    }

    public function testGetPreferedType()
    {
        $this->assertSame('application/json', $this->requestContext->getPreferedType(['text/csv', 'application/json']));
        $this->assertSame('application/json', $this->requestContext->getPreferedType(['application/json', 'text/markdown']));
        $this->assertSame('text/markdown', $this->requestContext->getPreferedType(['text/markdown', 'text/csv']));
    }

    public function testGetAll()
    {
        $this->assertSame(['q' => 'fizzbuzz', 'o' => 'trending', 'foo' => 'bar', 'fizz' => ''], $this->requestContext->getAll());
    }

    public function testGetInput()
    {
        $this->assertSame('fizzbuzz', $this->requestContext->getInput('q', 'failure'));
        $this->assertSame('success', $this->requestContext->getInput('f', 'success'));
        $this->assertSame('bar', $this->requestContext->getInput('foo', 'failure'));

    }

    public function testHasInput()
    {
        $this->assertTrue($this->requestContext->hasInput('q'));
        $this->assertFalse($this->requestContext->hasInput('f'));
    }

    public function testHasAnyInput()
    {
        $this->assertTrue($this->requestContext->hasAnyInput(['q', 'f', 'b']));
        $this->assertTrue($this->requestContext->hasAnyInput(['o']));
        $this->assertFalse($this->requestContext->hasAnyInput(['p']));
        $this->assertFalse($this->requestContext->hasAnyInput(['f', 'z', 'k']));
    }

    public function testGetQuery()
    {
        $this->assertSame('fizzbuzz', $this->requestContext->getQuery('q', 'failure'));
        $this->assertSame('success', $this->requestContext->getQuery('f', 'success'));
        $this->assertSame('success', $this->requestContext->getQuery('foo', 'success'));
    }

    public function testIsMethod()
    {
        $this->assertTrue($this->requestContext->isMethod('GET'));
        $this->assertFalse($this->requestContext->isMethod('POST'));
    }

    public function testIsFilled()
    {
        $this->assertTrue($this->requestContext->isFilled('q'));
        $this->assertTrue($this->requestContext->isFilled('foo'));
        $this->assertFalse($this->requestContext->isFilled('buzz'));
    }

    public function testIsNotFilled()
    {
        $this->assertFalse($this->requestContext->isNotFilled('q'));
        $this->assertFalse($this->requestContext->isNotFilled('foo'));
        $this->assertTrue($this->requestContext->isNotFilled('buzz'));
    }

    public function testIsMissing()
    {
        $this->assertFalse($this->requestContext->isMissing('q'));
        $this->assertFalse($this->requestContext->isMissing('foo'));
        $this->assertTrue($this->requestContext->isMissing('buzz'));
    }
}
