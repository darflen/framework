<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Factory;

use Darflen\Framework\Http\RequestHandler;

class RequestHandlerFactory
{
    public function createRequestHandler(array $stack, ?callable $resolver = null)
    {
        return new RequestHandler($stack, $resolver);
    }
}
