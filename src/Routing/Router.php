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

    private array $routes = [];

    public function __construct(RequestHandlerFactory $requestHandlerFactory)
    {
        $this->requestHandlerFactory = $requestHandlerFactory;
    }

    public function map(string|array $methods, string $path, RequestHandlerInterface|callable $handler): void
    {
        $route = new Route($methods, $path, $handler);
        $this->routes[] = $route;
    }

    public function dispatch(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $path = $serverRequest->getUri()->getPath();
        $method = $serverRequest->getMethod();
        foreach ($this->routes as $route) {
            if ($path === $route->getPath()) {
                if (!in_array($method, $route->getMethods())) {
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
