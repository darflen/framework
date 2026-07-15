<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Factory;

use Darflen\Framework\Http\ServerRequest;
use Override;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class ServerRequestFactory implements ServerRequestFactoryInterface
{
    #[Override]
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        return new ServerRequest($method, $uri, serverParams: $serverParams);
    }
}
