<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use Override;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class ServerRequest extends Request implements ServerRequestInterface
{
    private array $serverParams = [];

    private array $cookiesParams = [];

    private array $queryParams = [];

    private array $uploadedFiles = [];

    private ?StreamInterface $parsedBody;

    private array $attributes = [];

    public function __construct(string $method, string|UriInterface $uri, array $headers = [], ?StreamInterface $body = null, string $version = '1.1', array $serverParams = [], array $cookiesParams = [], array $queryParams = [], array $uploadedFiles = [], array $attributes = [], StreamInterface|null $parsedBody = null)
    {
        $this->setUri($uri);
        $this->setMethod($method);
        $this->setProtocolVersion($version);
        $this->setHeaders($headers);
        if (!$this->hasHeader('host')) {
            $this->setHeader('host', $uri->getHost());
        }
        $this->setStream($body);
        $this->serverParams = $serverParams;
        $this->cookiesParams = $cookiesParams;
        $this->queryParams = $queryParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->attributes = $attributes;
        $this->parsedBody = $parsedBody;
    }

    #[Override]
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    #[Override]
    public function getCookieParams(): array
    {
        return $this->cookiesParams;
    }

    #[Override]
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        $clone = clone $this;
        $this->cookiesParams = $cookies;
        return $clone;
    }

    #[Override]
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    #[Override]
    public function withQueryParams(array $query): ServerRequestInterface
    {
        $clone = clone $this;
        $this->queryParams = $query;
        return $clone;
    }

    #[Override]
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    #[Override]
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        $clone = clone $this;
        $this->uploadedFiles = $uploadedFiles;
        return $clone;
    }

    #[Override]
    public function getParsedBody(): ?StreamInterface
    {
        return $this->parsedBody;
    }

    #[Override]
    public function withParsedBody($data): ServerRequestInterface
    {
        $clone = clone $this;
        $this->parsedBody = $data;
        return $clone;
    }

    #[Override]
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    #[Override]
    public function getAttribute(string $name, $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    #[Override]
    public function withAttribute(string $name, $value): ServerRequestInterface
    {
        $clone = clone $this;
        $this->attributes[$name] = $value;
        return $clone;
    }

    #[Override]
    public function withoutAttribute(string $name): ServerRequestInterface
    {
        $clone = clone $this;
        unset($this->attributes[$name]);
        return $clone;
    }
}
