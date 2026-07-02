<?php

declare(strict_types=1);

namespace Darflen\Framework\Routing;

use Darflen\Framework\Http\Factory\RequestHandlerFactory;
use Darflen\Framework\Routing\Exceptions\MethodNotAllowedException;
use Darflen\Framework\Routing\Exceptions\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Darflen\Framework\Routing\Route;

class Router
{
    private RequestHandlerFactory $requestHandlerFactory;

    public function __construct(RequestHandlerFactory $requestHandlerFactory)
    {
        $this->requestHandlerFactory = $requestHandlerFactory;
    }

    protected function match(Route $route, string $routerPath, string $path): array
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routerPath);
        $pattern = '#^' . $pattern . '$#';
        $matched = preg_match($pattern, $path, $matches);
        $matches = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        foreach ($route->getAttribute('constraints', []) as $parameter => $constraint) {
            if (isset($matches[$parameter]) && !preg_match('/' . $constraint . '/', $matches[$parameter])) {
                $matched = false;
            }
        }
        return [
            'matched' => $matched,
            'matches' => $matches
        ];
    }

    public function dispatch(array $routes, ServerRequestInterface $serverRequest): ResponseInterface
    {
        $path = $serverRequest->getUri()->getPath();
        $method = $serverRequest->getMethod();
        foreach ($routes as $route) {
            $matches = $this->match($route, $route->getPath(), $path);
            if ($matches['matched']) {
                if (!in_array($method, $route->getMethods()) && $method !== 'ANY') {
                    throw new MethodNotAllowedException('Method is not allowed');
                }
                $handler = $route->getHandler();
                $stack = $route->getMiddlewares();
                $stack[] = $handler;
                $requestHandler = $this->requestHandlerFactory->createRequestHandler($stack);
                $serverRequest = $serverRequest->withAttribute('args', $matches['matches']);
                return $requestHandler->handle($serverRequest);
            }
        }

        throw new NotFoundException('No route matched');
    }
}
