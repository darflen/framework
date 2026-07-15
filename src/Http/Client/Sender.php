<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Client;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Sender
{
    private ClientInterface $client;

    private StreamFactoryInterface $streamFactory;

    private RequestFactoryInterface $requestFactory;

    public function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
    {
        $this->client = $client;
        $this->streamFactory = $streamFactory;
        $this->requestFactory = $requestFactory;
    }

    public function request(string $method, string|UriInterface $uri, ?array $headers = null, StreamInterface|string|null $body = null): ResponseInterface
    {
        $request = $this->requestFactory->createRequest($method, $uri);
        if (!is_null($headers)) {
            foreach ($headers as $name => $values) {
                $request = $request->withAddedHeader($name, $values);
            }
        }
        if (!is_null($body)) {
            if (is_string($body)) {
                $body = $this->streamFactory->createStream($body);
            }
            $request = $request->withBody($body);
        }
        return $this->client->sendRequest($request);
    }

    public function get(string|UriInterface $uri, ?array $headers = null): ResponseInterface
    {
        return $this->request('GET', $uri, $headers);
    }

    public function post(string|UriInterface $uri, ?array $headers = null, StreamInterface|string|null $body = null): ResponseInterface
    {
        return $this->request('POST', $uri, $headers, $body);
    }

    public function put(string|UriInterface $uri, ?array $headers = null, StreamInterface|string|null $body = null): ResponseInterface
    {
        return $this->request('PUT', $uri, $headers, $body);
    }

    public function patch(string|UriInterface $uri, ?array $headers = null, StreamInterface|string|null $body = null): ResponseInterface
    {
        return $this->request('PATCH', $uri, $headers, $body);
    }

    public function delete(string|UriInterface $uri, ?array $headers = null): ResponseInterface
    {
        return $this->request('DELETE', $uri, $headers);
    }

    public function head(string|UriInterface $uri, ?array $headers = null): ResponseInterface
    {
        return $this->request('HEAD', $uri, $headers);
    }
}
