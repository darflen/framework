<?php

declare(strict_types=1);

namespace Darflen\Framework\App;

use Darflen\Framework\Http\Factory\RequestHandlerFactory;
use Darflen\Framework\Http\Response;
use Darflen\Framework\Routing\RouteCollector;
use Darflen\Framework\Routing\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Kernel
{
    private RequestHandlerFactory $requestHandlerFactory;

    private App $app;

    private Router $router;

    private RouteCollector $routeCollector;

    public function __construct(App $app, RequestHandlerFactory $requestHandlerFactory, Router $router, RouteCollector $routeCollector)
    {
        $this->app = $app;
        $this->requestHandlerFactory = $requestHandlerFactory;
        $this->router = $router;
        $this->routeCollector = $routeCollector;
    }

    public function handle(ServerRequestInterface $serverRequest): ResponseInterface
    {
        $router = $this->router;
        $routes = $this->routeCollector->getRoutes();
        $stack = $this->app->getMiddlewares();
        $stack[] = function (ServerRequestInterface $request) use ($router, $routes): ResponseInterface {
            try {
                return $router->dispatch($routes, $request);
            } catch (\Throwable $e) {
                return new Response(500, '', [], 'Something Went Wrong! ' . (string) $e);
            }
        };
        $serverHandler = $this->requestHandlerFactory->createRequestHandler($stack);
        return $serverHandler->handle($serverRequest);
    }
}
