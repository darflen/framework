<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Factory;

use Override;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Darflen\Framework\Http\Request;

class RequestFactory implements RequestFactoryInterface
{
    #[Override]
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request($method, $uri);
    }
}
