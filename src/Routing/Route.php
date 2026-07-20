<?php

declare(strict_types=1);

namespace Darflen\Framework\Routing;

use Darflen\Framework\Support\Arr;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Route
{
    private array $attributes = [];

    private string $name = '';

    private string $host = '';

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

    public function getHost(): string
    {
        return $this->host;
    }

    public function getMethods(): array
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

    public function withHost(string $host): self
    {
        $clone = clone $this;
        $clone->host = $host;
        return $clone;
    }

    public function withMethods(array $methods): self
    {
        $clone = clone $this;
        $clone->methods = $methods;
        return $clone;
    }

    public function withAddedMethod(string $method): self
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

    public function withHandler(RequestHandlerInterface|callable|array $handler): self
    {
        $clone = clone $this;
        $clone->handler = $handler;
        return $clone;
    }

    public function withMiddlewares(array $middlewares): self
    {
        $clone = clone $this;
        $clone->middlewares = $middlewares;
        return $clone;
    }

    public function withAddedMiddleware(MiddlewareInterface|callable|array $middleware): self
    {
        $clone = clone $this;
        array_push($clone->middlewares, $middleware);
        return $clone;
    }

    public function withAttribute(string $name, mixed $value): self
    {
        $clone = clone $this;
        Arr::set($clone->attributes, $name, $value);
        return $clone;
    }

    public function withoutAttribute(string $name): self
    {
        $clone = clone $this;
        Arr::remove($clone->attributes, $name);
        return $clone;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return Arr::get($this->attributes, $name, $default);
    }
}
