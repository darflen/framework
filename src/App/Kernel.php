<?php

declare(strict_types=1);

namespace Darflen\Framework\App;

use Darflen\Framework\Http\RequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Kernel
{
    private RequestHandler $requestHandler;

    public function __construct(RequestHandler $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    public function handle(ServerRequestInterface $serverRequest): ResponseInterface
    {
        return $this->requestHandler->handle($serverRequest);
    }
}
