<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use InvalidArgumentException;

/**
 * Value object representing a Cookie.
 */
class Cookie
{
    private const string COOKIE_DATE_FORMAT = 'D, d M Y H:i:s \G\M\T';

    private string $name;

    private string $value;

    private int $expiresAt;

    private string $path;

    private string $domain;

    private bool $secure;

    private bool $httpOnly;

    private string $sameSite;

    public function __construct(string $name, string $value = '', int $expiresAt = 0, string $path = '', string $domain = '', bool $secure = false, bool $httpOnly = false, string $sameSite = '')
    {
        $this->name = $name;
        $this->value = $value;
        $this->expiresAt = $expiresAt;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
        $this->validateSameSite($sameSite);
        $this->sameSite = $sameSite;
    }

    public function __toString()
    {
        return $this->toHeaderValue();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpiration(): int
    {
        return $this->expiresAt;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }

    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    public function getSameSite(): string
    {
        return $this->sameSite;
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function withValue(string $value): self
    {
        $clone = clone $this;
        $clone->value = $value;
        return $clone;
    }

    public function withExpiration(int $expiresAt): self
    {
        $clone = clone $this;
        $clone->expiresAt = $expiresAt;
        return $clone;
    }

    public function withPath(string $path): self
    {
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    public function withDomain(string $domain): self
    {
        $clone = clone $this;
        $clone->domain = $domain;
        return $clone;
    }

    public function withSecure(bool $secure): self
    {
        $clone = clone $this;
        $clone->secure = $secure;
        return $clone;
    }

    public function withHttpOnly(bool $httpOnly): self
    {
        $clone = clone $this;
        $clone->httpOnly = $httpOnly;
        return $clone;
    }

    public function withSameSite(string $sameSite): self
    {
        $this->validateSameSite($sameSite);
        $clone = clone $this;
        $clone->sameSite = $sameSite;
        return $clone;
    }

    private function validateSameSite(string $sameSite): void
    {
        $sameSite = ucfirst(strtolower($sameSite));
        if (!in_array($sameSite, ['', 'Lax', 'Strict', 'None'])) {
            throw new InvalidArgumentException('Not a valid value for sameSite attribute');
        }
        if ($sameSite === 'None' && !$this->isSecure()) {
            throw new InvalidArgumentException('Not a valid value for sameSite attribute when not secure');
        }
    }

    private function toHeaderValue(): string
    {
        $headerValue = $this->name . '=' . urlencode($this->value);
        if ($this->expiresAt !== 0) {
            $headerValue .= '; Expires=' . date(self::COOKIE_DATE_FORMAT, $this->expiresAt);
        }
        if (empty($this->path) === false) {
            $headerValue .= '; Path=' . $this->path;
        }
        if (empty($this->domain) === false) {
            $headerValue .= '; Domain=' . $this->domain;
        }
        if ($this->secure) {
            $headerValue .= '; Secure';
        }
        if ($this->httpOnly) {
            $headerValue .= '; HttpOnly';
        }
        if ($this->sameSite !== '') {
            $headerValue .= '; SameSite=' . $this->sameSite;
        }
        return $headerValue;
    }
}
