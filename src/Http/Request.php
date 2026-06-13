<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use InvalidArgumentException;
use Override;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request extends Message implements RequestInterface
{
    private UriInterface $uri;

    private string $method = 'GET';

    private const array AVAILABLE_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS', 'PURGE', 'TRACE', 'CONNECT'];

    private ?string $requestTarget = null;

    public function __construct(string $method, string|UriInterface $uri, array $headers = [], ?StreamInterface $body = null, string $version = '1.1')
    {
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }
        $this->uri = $uri;
        $this->validateHTTPMethod($method);
        $this->method = $method;
        $this->setProtocolVersion($version);
        $this->setHeaders($headers);
        if (!$this->hasHeader('host')) {
            $this->setHeader('host', $uri->getHost());
        }
        $this->setStream($body);
    }

    #[Override]
    public function getRequestTarget(): string
    {
        if (!is_null($this->requestTarget)) {
            return $this->requestTarget;
        }
        $this->requestTarget = $this->uri->getPath();
        $this->requestTarget = $this->requestTarget === '' ? '/' : $this->requestTarget . '?' . $this->uri->getQuery();
        return $this->requestTarget;
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
        $clone = clone $this;
        $this->validateHTTPMethod($method);
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
        if (!$preserveHost) {
            $this->setHeader('host', $uri->getHost());
        }
        return $clone;
    }

    public function validateHTTPMethod(string $method): void
    {
        $method = strtoupper($method);
        if (!in_array($method, self::AVAILABLE_METHODS)) {
            throw new InvalidArgumentException("Invalid method");
        }
    }
}
