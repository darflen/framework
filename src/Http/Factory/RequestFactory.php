<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Factory;

use Darflen\Framework\Http\Request;
use Override;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

class RequestFactory implements RequestFactoryInterface
{
    #[Override]
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request($method, $uri);
    }
}
