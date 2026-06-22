<?php

declare(strict_types=1);

namespace Darflen\Framework\Routing;

use Darflen\Framework\Http\Factory\RequestHandlerFactory;
use Darflen\Framework\Routing\Exceptions\MethodNotAllowedException;
use Darflen\Framework\Routing\Exceptions\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Router
{
    private RequestHandlerFactory $requestHandlerFactory;

    public function __construct(RequestHandlerFactory $requestHandlerFactory)
    {
        $this->requestHandlerFactory = $requestHandlerFactory;
    }

    public function dispatch(array $routes, ServerRequestInterface $serverRequest): ResponseInterface
    {
        $path = $serverRequest->getUri()->getPath();
        $method = $serverRequest->getMethod();
        foreach ($routes as $route) {
            if ($path === $route->getPath()) {
                if (!in_array($method, $route->getMethods()) && $method !== 'ANY') {
                    throw new MethodNotAllowedException('Method is not allowed');
                }
                $handler = $route->getHandler();
                $items = $route->getMiddlewares();
                $items[] = $handler;
                $requestHandler = $this->requestHandlerFactory->createRequestHandler($items);
                return $requestHandler->handle($serverRequest);
            }
        }

        throw new NotFoundException('No route matched');
    }
}
