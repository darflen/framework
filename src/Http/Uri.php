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

    private string $placeholderHost = 'placeholder.local';

    private const array AVAILABLE_SCHEMES = ['' => null, 'http' => 80, 'https' => 443];

    public function __construct(string $uri)
    {
        $uri = $this->urlEncodeUri($uri);
        $this->parser = $this->wrapException(fn () => new PHPUri($uri));
        if (is_null($this->parser->getHost())) {
            $this->parser = $this->parser->withHost($this->placeholderHost);
        }
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
        $clone = clone $this;
        if ($clone->parser->getHost() === $this->placeholderHost) {
            $clone->parser = $clone->parser->withHost(null);
        }
        return $clone->parser->toString();
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
        $host = $this->parser->getHost() ?? '';
        $host = $host === $this->placeholderHost ? '' : $host;
        return $host;
    }

    #[Override]
    public function getPort(): ?int
    {
        $port = $this->parser->getPort();
        if ((self::AVAILABLE_SCHEMES[$this->getScheme()] ?? '') === $port) {
            $port = null;
        }
        return $port;
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
        $path = $this->parser->getPath();
        $path = preg_replace('#(?<!/)//(?!/)#', '/', $path);
        return $path;
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
        $user = $this->urlEncodeUserInfo($user);
        if (!is_null($password)) {
            $password = $this->urlEncodeUserInfo($password);
        }
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

    private function urlEncodeUserInfo(string $value): string
    {
        return preg_replace_callback(
            '/(?:[^%a-zA-Z0-9_\-\.\~]+|%(?![A-Fa-f0-9]{2}))/',
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $value
        );
    }

    private function urlEncodeUri(string $value): string
    {
        return preg_replace_callback(
            '/(?:[^%a-zA-Z0-9\_\-\.\~\!\$\&\'\*\+\,\;\=\:\?\#\@\/\\\\]+|%(?![A-Fa-f0-9]{2}))/',
            function ($matches) {
                return rawurlencode($matches[0]);
            },
            $value
        );
    }
}
