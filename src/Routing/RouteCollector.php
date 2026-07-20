<?php

declare(strict_types=1);

namespace Darflen\Framework\Routing;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteCollector
{
    private array $routes = [];

    public function map(string|array $methods, string $path, RequestHandlerInterface|callable $handler): self
    {
        $route = new Route($methods, $path, $handler);
        $this->routes[] = $route;
        return $this;
    }

    public function get(string $path, RequestHandlerInterface|callable $handler): self
    {
        return $this->map('GET', $path, $handler);
    }

    public function post(string $path, RequestHandlerInterface|callable $handler): self
    {
        return $this->map('POST', $path, $handler);
    }

    public function put(string $path, RequestHandlerInterface|callable $handler): self
    {
        return $this->map('PUT', $path, $handler);
    }

    public function patch(string $path, RequestHandlerInterface|callable $handler): self
    {
        return $this->map('PATCH', $path, $handler);
    }

    public function delete(string $path, RequestHandlerInterface|callable $handler): self
    {
        return $this->map('DELETE', $path, $handler);
    }

    public function any(string $path, RequestHandlerInterface|callable $handler): self
    {
        return $this->map('ANY', $path, $handler);
    }

    public function addMiddleware(MiddlewareInterface|callable|array $middleware): self
    {
        $lastestRoute = array_key_last($this->routes);
        $this->routes[$lastestRoute] = $this->routes[$lastestRoute]->withAddedMiddleware($middleware);
        return $this;
    }

    public function addMiddlewares(array $middlewares): self
    {
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
        return $this;
    }

    public function setName(string $name): self
    {
        $lastestRoute = array_key_last($this->routes);
        $this->routes[$lastestRoute] = $this->routes[$lastestRoute]->withName($name);
        return $this;
    }

    public function setHost(string $host): self
    {
        $lastestRoute = array_key_last($this->routes);
        $this->routes[$lastestRoute] = $this->routes[$lastestRoute]->withHost($host);
        return $this;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function setConstraint(string $parameter, string $regex): self
    {
        $lastestRoute = array_key_last($this->routes);
        $this->routes[$lastestRoute] = $this->routes[$lastestRoute]->withAttribute('constraints.' . $parameter, $regex);
        return $this;
    }
}
