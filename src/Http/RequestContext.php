<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Provides enhanced data about a ServerRequest class
 */
class RequestContext
{
    private ServerRequestInterface $serverRequest;

    public function __construct(ServerRequestInterface $serverRequest)
    {
        $this->serverRequest = $serverRequest;
    }

    public function getPath(): ?string
    {
        return $this->serverRequest->getUri()->getPath();
    }

    public function getUrl(): ?string
    {
        $uri = $this->serverRequest->getUri();
        return $uri->getScheme() . '://' . $this->getHost() . $uri->getPath();
    }

    public function getFullUrl(): ?string
    {
        return (string) $this->serverRequest->getUri();
    }

    public function getFullUrlWithQuery(array $queries): ?string
    {
        $uri = $this->serverRequest->getUri();
        parse_str($uri->getQuery(), $query);
        $query = array_merge($query, $queries);
        $clone = $uri->withQuery(http_build_query($query, encoding_type: PHP_QUERY_RFC3986));
        return  (string) $clone;
    }

    public function getFullUrlWithoutQuery(array $queries): ?string
    {
        $uri = $this->serverRequest->getUri();
        parse_str($uri->getQuery(), $query);
        $query = array_diff_key($query, array_flip($queries));
        $clone = $uri->withQuery(http_build_query($query, encoding_type: PHP_QUERY_RFC3986));
        return (string) $clone;
    }

    public function getHost(): ?string
    {
        $server = $this->serverRequest->getServerParams();
        $actual = $this->serverRequest->getUri()->getHost();
        $fallback = $this->serverRequest->getHeaderLine('HOST');
        $fallback = $fallback === '' ? $server["SERVER_ADDR"] ?? null : $fallback;
        return $actual === '' ? $fallback : $actual;
    }

    public function getHttpHost(): ?string
    {
        return $this->getHost();
    }

    public function getOrigin(): ?string
    {
        return $this->serverRequest->getHeaderLine('Origin');
    }

    public function getSchemeAndHttpHost(): ?string
    {
        $server = $this->serverRequest->getServerParams();
        $actual = $this->serverRequest->getUri()->getHost();
        $fallback = $server["SERVER_ADDR"] ?? null;
        $host = $actual === '' ? $fallback : $actual;
        return $this->serverRequest->getUri()->getScheme() . '://' . $host;
    }

    public function getAuthorization(): ?string
    {
        $header = $this->serverRequest->getHeaderLine('Authorization');
        return $header === '' ? null : $header;
    }

    public function getBearerToken(): ?string
    {
        $header = $this->getAuthorization() ?? '';
        preg_match('/Bearer\s(\S+)/i', $header, $matches);
        return $matches[1];
    }

    public function getIp(): ?string
    {
        $server = $this->serverRequest->getServerParams();
        $actual = $this->serverRequest->getHeaderLine('CF-CONNECTING-IP');
        return $actual === '' ? $server['REMOTE_ADDR'] ?? null : $actual;
    }

    public function getIps(): array
    {
        return explode(',', $this->serverRequest->getHeaderLine('X-FORWARDED-FOR'));
    }

    private function getRawAcceptableContentTypes(): array
    {
        $header = $this->serverRequest->getHeaderLine('Accept');
        $acceptableTypes = explode(',', $header);
        $typesWithParams = array_map("trim", $acceptableTypes);
        return $typesWithParams;
    }

    public function getAcceptableContentTypes(): array
    {
        $typesWithParams = $this->getRawAcceptableContentTypes();
        $typesWithParams = array_map(function ($item) {
            $stuff = explode(';', $item);
            return $stuff[0];
        }, $typesWithParams);
        return $typesWithParams;
    }

    public function isAcceptableTypes(array $accepts): bool
    {
        $all = $this->getAcceptableContentTypes();
        return empty(array_diff($accepts, $all));
    }

    public function getPreferedType(array $accepts): ?string
    {
        $acceptable = $this->getRawAcceptableContentTypes();
        $types = array_map(function ($item) {
            $stuff = explode(';', $item);
            return [$stuff[0] => str_replace('q=', '', $stuff[1] ?? '1.0')];
        }, $acceptable);
        usort($types, function ($a, $b) {
            return current($b) <=> current($a);
        });
        $score = PHP_INT_MAX;
        foreach ($accepts as $accept) {
            foreach ($types as $key => $type) {
                if (key($type) === $accept && $key <= $score) {
                    $score = $key;
                }
            }
        }
        if (isset($types[$score])) {
            return key($types[$score]);
        }
        return null;
    }

    public function getAll(): array
    {
        $queryParams = $this->serverRequest->getQueryParams();
        $parsedBody = $this->serverRequest->getParsedBody();
        return array_merge($queryParams, $parsedBody ?? []);
    }

    public function getInput(string $input, mixed $default = null): mixed
    {
        return $this->getAll()[$input] ?? $default;
    }

    public function hasInput(string $input): bool
    {
        return isset($this->getAll()[$input]);
    }

    public function hasAnyInput(array $inputs): bool
    {
        return !empty(array_intersect_key($this->getAll(), array_flip($inputs)));
    }

    public function getQuery(string $input, mixed $default = null): mixed
    {
        return $this->serverRequest->getQueryParams()[$input] ?? $default;
    }

    public function isMethod(string $method): bool
    {
        return $this->serverRequest->getMethod() === $method;
    }

    public function isFilled(string $input): bool
    {
        return $this->getInput($input, '') !== '';
    }

    public function isNotFilled(string $input): bool
    {
        return !$this->isFilled($input);
    }

    public function isMissing(string $input): bool
    {
        return $this->getInput($input) === null;
    }
}
