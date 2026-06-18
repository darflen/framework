<?php

declare(strict_types=1);

namespace Darflen\Framework\Http;

use Psr\Http\Message\ServerRequestInterface;

class HttpRequest
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
        return $uri->getScheme() . '://' . $uri->getHost() . $uri->getPath();
    }

    public function getFullUrl(): ?string
    {
        return (string) $this->serverRequest->getUri();
    }

    public function getHost(): ?string
    {
        $server = $this->serverRequest->getServerParams();
        $actual = $this->serverRequest->getUri()->getHost();
        $fallback = $server["HTTP_HOST"] ?? $server["SERVER_ADDR"] ?? null;
        return $actual === '' ? $fallback : $actual;
    }

    public function getHttpHost(): ?string
    {
        return $this->getHost();
    }

    public function getOrigin(): string
    {
        return $this->serverRequest->getHeaderLine('Origin');
    }

    public function getSchemeAndHttpHost(): ?string
    {
        $server = $this->serverRequest->getServerParams();
        $actual = $this->serverRequest->getUri()->getHost();
        $fallback = $server["SERVER_ADDR"] ?? null;
        return $actual === '' ? $fallback : $actual;
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
        return $server['HTTP_CF_CONNECTING_IP'] ?? $server['REMOTE_ADDR'] ?? null;
    }

    public function getIps(): array
    {
        return explode(',', $this->serverRequest->getHeaderLine('X_FORWARDED_FOR'));
    }

    public function getAcceptableContentTypes(): array
    {
        $header = $this->serverRequest->getHeaderLine('Accept');
        $acceptableTypes = explode(',', $header);
        $typesWithParams = array_map("trim", $acceptableTypes);
        return $typesWithParams;
    }

    public function isAcceptableTypes(array $accepts): bool
    {
        $all = $this->getAcceptableContentTypes();
        return empty(array_diff($accepts, $all));
    }

    public function getPreferedType(array $accepts): ?string
    {
        $acceptable = $this->getAcceptableContentTypes();
        $types = array_map(function ($item) {
            $stuff = explode(';', $item);
            return [$stuff[0] => str_replace('q=', '', $stuff[1] ?? '1.0')];
        }, $acceptable);
        usort($types, function ($a, $b) {
            return current($a) <=> current($b);
        });
        foreach ($accepts as $accept) {
            foreach ($types as $type) {
                if ($type[0] === $accept) {
                    return $type[0];
                }
            }
        }
        return null;
    }

    public function getMostPreferedType(string $accept): ?string
    {
        return $this->getPreferedType([$accept]);
    }

    public function getAll(): array
    {
        $queryParams = $this->serverRequest->getQueryParams();
        $parsedBody = $this->serverRequest->getParsedBody();
        return array_merge($queryParams, $parsedBody);
    }

    public function getInput(string $input, mixed $default = null): mixed
    {
        return $this->getAll()[$input] ?? $default;
    }

    public function getQuery(string $input, mixed $default = null): mixed
    {
        return $this->serverRequest->getQueryParams()[$input] ?? $default;
    }
}
