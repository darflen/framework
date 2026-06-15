<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use InvalidArgumentException;
use Override;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Message implements MessageInterface
{
    private array $headerNames = [];

    private array $headers = [];

    private string $protocol = '1.1';

    private const array AVAILABLE_PROTOCOL_VERSIONS = ['1.0', '1.1', '2.0', '2', '3.0', '3'];

    private ?StreamInterface $body;

    #[Override]
    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    #[Override]
    public function withProtocolVersion(string $version): MessageInterface
    {
        $clone = clone $this;
        $this->validateProtocolVersion($version);
        $clone->protocol = $version;
        return $clone;
    }

    #[Override]
    public function getHeaders(): array
    {
        $result = [];
        foreach ($this->headerNames as $key => $name) {
            $result[$name] = $this->headers[$key];
        }
        return $result;
    }

    #[Override]
    public function getHeader(string $name): array
    {
        $name = strtolower($name);
        return isset($this->headerNames[$name]) ? $this->headers[$name] : [];
    }

    #[Override]
    public function getHeaderLine(string $name): string
    {
        $headers = $this->getHeader($name);
        foreach ($headers as $key => $header) {
            if (is_array($headers[$key])) {
                $headers[$key] = implode(',', $headers[$key]);
            }
        }
        return implode(',', $headers);
    }

    #[Override]
    public function hasHeader(string $name): bool
    {
        return $this->getHeader($name) !== [];
    }

    #[Override]
    public function withHeader(string $name, $value): MessageInterface
    {
        $clone = clone $this;
        $this->validateHeaderName($name);
        if (is_string($value)) {
            $value = [$value];
        }
        $this->validateHeaderData($name, $value);
        $lowerName = strtolower($name);
        $clone->headerNames[$lowerName] = $name;
        foreach ($value as $singleValue) {
            $this->validateHeaderValue($singleValue);
        }
        $clone->headers[$lowerName] = $value;
        return $clone;
    }

    #[Override]
    public function withoutHeader(string $name): MessageInterface
    {
        $clone = clone $this;
        $lowerName = strtolower($name);
        unset($clone->headerNames[$lowerName]);
        unset($clone->headers[$lowerName]);
        return $clone;
    }

    #[Override]
    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $clone = clone $this;
        $this->validateHeaderName($name);
        if (is_string($value)) {
            $value = [$value];
        }
        $this->validateHeaderData($name, $value);
        $lowerName = strtolower($name);
        $clone->headerNames[$lowerName] = $name;
        foreach ($value as $singleValue) {
            $this->validateHeaderValue($singleValue);
        }
        $clone->headers[$lowerName] = array_merge_recursive($clone->headers[$lowerName] ?? [], $value);
        return $clone;
    }

    #[Override]
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    #[Override]
    public function withBody(StreamInterface $body): MessageInterface
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    private function validateProtocolVersion(string $version): void
    {
        if (!in_array($version, self::AVAILABLE_PROTOCOL_VERSIONS)) {
            throw new \InvalidArgumentException(sprintf('Invalid HTTP protocol version: "%s"', $version));
        }
    }

    private function validateHeaderValue(string $value): void
    {
        if (preg_match('/[\x00\x0D\x0A]/', $value)) {
            throw new \InvalidArgumentException(sprintf('Invalid character in header value: "%s"', $value));
        }
    }

    private function validateHeaderName(string $name): void
    {
        if (preg_match('/[\x00-\x20]/', $name)) {
            throw new \InvalidArgumentException(sprintf('Invalid character in header name: "%s"', $name));
        }
    }

    private function validateHeaderData(string $name, mixed $value): void
    {
        if (!is_string($value) && !is_array($value) || (is_array($value) && empty($value)) || (is_string($value) && $value === '')) {
            throw new InvalidArgumentException('Header value must be a non-empty array or a string');
        }
        if ($name === '') {
            throw new InvalidArgumentException("Header name must not be empty");
        }
    }

    protected function setProtocolVersion(string $version): void
    {
        $this->validateProtocolVersion($version);
        $this->protocol = $version;
    }

    protected function setHeaders(array $headers): void
    {
        foreach ($headers as $name => $values) {
            $lowerName = strtolower($name);
            $this->validateHeaderName($name);
            $this->headerNames[$lowerName] = $name;
            foreach ($values as $value) {
                $this->validateHeaderValue($value);
                $this->headers[$lowerName] = array_merge($this->headers[$lowerName] ?? [], (array) $value);
            }
        }
    }

    protected function setHeader(string $name, string $value): void
    {
        $this->validateHeaderName($name);
        $this->validateHeaderValue($value);
        $lowerName = strtolower($name);
        $this->headerNames[$lowerName] = $name;
        $this->headers[$lowerName] = (array) $value;
    }

    protected function setStream(?StreamInterface $body)
    {
        $this->body = $body;
    }
}
