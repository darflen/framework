<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Factory;

use Darflen\Framework\Http\RequestContext;
use Psr\Http\Message\ServerRequestInterface;

class RequestContextFactory
{
    public function createRequestContext(ServerRequestInterface $serverRequest): RequestContext
    {
        return new RequestContext($serverRequest);
    }
}
