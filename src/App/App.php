<?php

declare(strict_types=1);

namespace Darflen\Framework\App;

use Psr\Container\ContainerInterface;
use Darflen\Framework\Config\Config;
use Psr\Http\Server\MiddlewareInterface;

class App
{
    private static ContainerInterface $container;

    private static ?string $projectDir = null;

    private static array $routes = [];

    private static array $middlewares = [];

    public function __construct(string $projectDir, ContainerInterface $container)
    {
        self::$projectDir = $projectDir;
        self::$container = $container;
    }

    public function create(): void
    {
        Config::setup(self::$projectDir . '/config', self::$projectDir)->create();
        foreach (self::$routes as $route) {
            include_once $route;
        }
    }

    public function getProjectDir(): ?string
    {
        return self::$projectDir;
    }

    public function getRoutes(): array
    {
        return self::$routes;
    }

    public function getMiddlewares(): array
    {
        return self::$middlewares;
    }

    public function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    public function setRouting(array $routes): self
    {
        self::$routes = $routes;
        return $this;
    }

    public function addMiddleware(MiddlewareInterface|callable|array $middleware): self
    {
        array_push(self::$middlewares, $middleware);
        return $this;
    }

    public function setMiddlewares(array $middlewares): self
    {
        self::$middlewares = $middlewares;
        return $this;
    }

    public static function getApp(): static
    {
        return new self(self::$projectDir, self::$container);
    }
}
