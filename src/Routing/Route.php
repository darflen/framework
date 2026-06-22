<?php

declare(strict_types=1);

namespace Darflen\Framework\Routing;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class Route
{
    private string $name;

    private array $methods;

    private string $path;

    /**
     * @var RequestHandlerInterface|callable|array
     */
    private $handler;

    private array $middlewares = [];

    public function __construct(string|array $methods, string $path, RequestHandlerInterface|callable|array $handler)
    {
        if (is_string($methods)) {
            $methods = [$methods];
        }
        $this->methods = $methods;
        $this->path = $path;
        $this->handler = $handler;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMethods(): string|array
    {
        return $this->methods;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler(): RequestHandlerInterface|callable|array
    {
        return $this->handler;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function withName(string $name): self
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    public function withMethods(array $methods): self
    {
        $clone = clone $this;
        $clone->methods = $methods;
        return $clone;
    }

    public function withAddedMethod(string $method)
    {
        $clone = clone $this;
        array_push($clone->methods, $method);
        return $clone;
    }

    public function withPath(string $path): self
    {
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    public function withHandler(RequestHandlerInterface|callable|array $handler)
    {
        $clone = clone $this;
        $clone->handler = $handler;
        return $clone;
    }

    public function withMiddlewares(array $middlewares)
    {
        $clone = clone $this;
        $clone->middlewares = $middlewares;
        return $clone;
    }

    public function withAddedMiddleware(MiddlewareInterface|callable|array $middleware)
    {
        $clone = clone $this;
        array_push($clone->middlewares, $middleware);
        return $clone;
    }
}
