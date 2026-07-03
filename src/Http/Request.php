<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use Darflen\Framework\Support\Factory\StreamFactory;
use InvalidArgumentException;
use Override;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{
    private UriInterface $uri;

    private string $method = 'GET';

    private ?string $requestTarget = null;

    public function __construct(string $method, string|UriInterface $uri, array $headers = [], ?StreamInterface $body = null, string $version = '1.1')
    {
        $this->setUri($uri);
        $this->setMethod($method);
        $this->setProtocolVersion($version);
        $this->setHeaders($headers);
        if (!$this->hasHeader('host')) {
            $this->setHeader('host', $this->uri->getHost());
        }
        if (is_null($body)) {
            $body = new StreamFactory();
            $body = $body->createStream('');
        }
        $this->setStream($body);
    }

    #[Override]
    public function getRequestTarget(): string
    {
        if (!is_null($this->requestTarget)) {
            return $this->requestTarget;
        }
        $target = $this->uri->getPath();
        $query = $this->uri->getQuery();
        $target = $target === '' ? '/' : $target . ($query !== '' ? '?' . $query : '');
        return $target;
    }

    #[Override]
    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;
        return $clone;
    }

    #[Override]
    public function getMethod(): string
    {
        return $this->method;
    }

    #[Override]
    public function withMethod(string $method): RequestInterface
    {
        if ($method === '') {
            throw new InvalidArgumentException('Method string must not be empty');
        }
        $clone = clone $this;
        $clone->method = $method;
        return $clone;
    }

    #[Override]
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    #[Override]
    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        $clone = clone $this;
        $clone->uri = $uri;
        if (!$preserveHost && $uri->getHost() !== '') {
            $clone->setHeader('Host', $uri->getHost());
        }
        if ($preserveHost && $uri->getHost() !== '' && $clone->getHeaderLine('Host') === '') {
            $clone->setHeader('Host', $uri->getHost());
        }
        return $clone;
    }

    protected function setUri(string|UriInterface $uri): void
    {
        if (is_string($uri)) {
            $uri = new Uri($uri);
        }
        $this->uri = $uri;
    }

    protected function setMethod(string $method): void
    {
        $this->method = $method;
    }
}
