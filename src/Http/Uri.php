<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use InvalidArgumentException;
use Override;
use Psr\Http\Message\UriInterface;
use Uri\InvalidUriException;
use Uri\Rfc3986\Uri as PHPUri;

class Uri implements UriInterface
{
    private PHPUri $parser;

    public function __construct(string $uri)
    {
        $this->parser = $this->wrapException(fn () => new PHPUri($uri));
    }

    private function wrapException(callable $function): PHPUri
    {
        try {
            return $function();
        } catch (InvalidUriException $error) {
            throw new InvalidArgumentException($error->getMessage(), previous: $error);
        }
    }

    #[Override]
    public function __toString(): string
    {
        return $this->parser->toString();
    }

    #[Override]
    public function getScheme(): string
    {
        return $this->parser->getScheme() ?? '';
    }

    #[Override]
    public function getAuthority(): string
    {
        return ($this->getUserInfo() !== '' ? $this->getUserInfo() . '@' : '') . $this->getHost() . (!is_null($this->getPort()) ? ':' . $this->getPort() : '');
    }

    #[Override]
    public function getUserInfo(): string
    {
        return $this->parser->getUserInfo() ?? '';
    }

    #[Override]
    public function getHost(): string
    {
        return $this->parser->getHost() ?? '';
    }

    #[Override]
    public function getPort(): ?int
    {
        return $this->parser->getPort();
    }

    #[Override]
    public function getQuery(): string
    {
        return $this->parser->getQuery() ?? '';
    }

    #[Override]
    public function getFragment(): string
    {
        return $this->parser->getFragment() ?? '';
    }

    #[Override]
    public function getPath(): string
    {
        return $this->parser->getPath();
    }

    #[Override]
    public function withScheme(string $scheme): UriInterface
    {
        $clone = clone $this;
        $clone->parser = $clone->wrapException(fn () => $clone->parser->withScheme($scheme));
        return $clone;
    }

    #[Override]
    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $clone = clone $this;
        $userInfo = $user . (!empty($password) || $password === '0' ? ':' . $password : '');
        $clone->parser = $clone->wrapException(fn () => $clone->parser->withUserInfo($userInfo));
        return $clone;
    }

    #[Override]
    public function withHost(string $host): UriInterface
    {
        $clone = clone $this;
        $clone->parser = $clone->wrapException(fn () => $clone->parser->withHost($host));
        return $clone;
    }

    #[Override]
    public function withPort(?int $port): UriInterface
    {
        $clone = clone $this;
        $clone->parser = $clone->wrapException(fn () => $clone->parser->withPort($port));
        return $clone;
    }

    #[Override]
    public function withPath(string $path): UriInterface
    {
        $clone = clone $this;
        $clone->parser = $clone->wrapException(fn () => $clone->parser->withPath($path));
        return $clone;
    }

    #[Override]
    public function withQuery(string $query): UriInterface
    {
        $clone = clone $this;
        $clone->parser = $clone->wrapException(fn () => $clone->parser->withQuery($query));
        return $clone;
    }

    #[Override]
    public function withFragment(string $fragment): UriInterface
    {
        $clone = clone $this;
        $clone->parser = $clone->wrapException(fn () => $clone->parser->withFragment($fragment));
        return $clone;
    }
}
