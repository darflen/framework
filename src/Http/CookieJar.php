<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class CookieJar
{
    private array $cookies = [];

    public function __construct(array $cookies = [])
    {
        $this->validateCookies($cookies);
        foreach ($cookies as $cookie) {
            $this->cookies[$cookie->getName()] = $cookie;
        }
    }

    public function getCookie(string $cookie): ?Cookie
    {
        return isset($this->cookies[$cookie]) ? $this->cookies[$cookie] : null;
    }

    public function getCookieValue(string $cookie, mixed $default = null): mixed
    {
        $cookie = $this->getCookie($cookie);
        return isset($cookie) ? $cookie->getValue() : $default;
    }

    public function getAll(): array
    {
        return $this->cookies;
    }

    public function getCount(): int
    {
        return count($this->cookies);
    }

    public function withCookie(Cookie $cookie): self
    {
        $clone = clone $this;
        $clone->cookies[$cookie->getName()] = $cookie;
        return $clone;
    }

    public function withCookies(array $cookies): self
    {
        $this->validateCookies($cookies);
        $clone = clone $this;
        foreach ($cookies as $cookie) {
            $clone->cookies[$cookie->getName()] = $cookie;
        }
        return $clone;
    }

    public function withoutCookie(string $cookie): self
    {
        $clone = clone $this;
        unset($clone->cookies[$cookie]);
        return $clone;
    }

    public function hasCookie(string $cookie): bool
    {
        return isset($this->cookies[$cookie]);
    }

    public function sendAll(ResponseInterface $response, bool $withoutResponseCookies = true): ResponseInterface
    {
        if ($withoutResponseCookies) {
            $response = $response->withoutHeader('Set-Cookie');
        }
        foreach ($this->cookies as $cookie) {
            $response->withAddedHeader('Set-Cookie', (string) $cookie);
        }
        return $response;
    }

    public function clearAll(): self
    {
        $clone = clone $this;
        $clone->cookies = [];
        return $clone;
    }

    private function validateCookies(array $cookies): void
    {
        foreach ($cookies as $cookie) {
            if (!($cookie instanceof Cookie)) {
                throw new InvalidArgumentException('Value is not a valid instace of Cookie');
            }
        }
    }
}
