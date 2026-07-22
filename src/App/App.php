<?php

declare(strict_types=1);

namespace Darflen\Framework\App;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class App
{
    private ContainerInterface $container;

    private ?string $projectDir = null;

    private array $routes = [];

    private array $middlewares = [];

    private static ?self $instance = null;

    public function __construct(string $projectDir, ContainerInterface $container)
    {
        $this->projectDir = $projectDir;
        $this->container = $container;
        self::$instance = $this;
    }

    public function boot(): void
    {
        foreach ($this->routes as $route) {
            include_once $route;
        }
    }

    public function getProjectDir(): ?string
    {
        return $this->projectDir;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setRouting(array $routes): self
    {
        $this->routes = $routes;
        return $this;
    }

    public function addMiddleware(MiddlewareInterface|callable|array $middleware): self
    {
        array_push($this->middlewares, $middleware);
        return $this;
    }

    public function setMiddlewares(array $middlewares): self
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }
}
