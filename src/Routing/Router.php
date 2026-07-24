<?php

declare(strict_types=1);

namespace Darflen\Framework\Routing;

use Darflen\Framework\Http\Factory\RequestHandlerFactory;
use Darflen\Framework\Routing\Exceptions\MethodNotAllowedException;
use Darflen\Framework\Routing\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    private RequestHandlerFactory $requestHandlerFactory;

    private ContainerInterface $container;

    public function __construct(RequestHandlerFactory $requestHandlerFactory, ContainerInterface $container)
    {
        $this->requestHandlerFactory = $requestHandlerFactory;
        $this->container = $container;
    }

    protected function matchPath(array $constraints, string $routerPath, string $path): array
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routerPath);
        $pattern = '#^' . $pattern . '$#';
        $matched = preg_match($pattern, $path, $matches);
        $matches = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        foreach ($constraints as $parameter => $constraint) {
            if (isset($matches[$parameter]) && !preg_match('/' . $constraint . '/', $matches[$parameter])) {
                $matched = false;
            }
        }
        return [
            'matched' => $matched,
            'matches' => $matches,
        ];
    }

    protected function match(Route $route, ServerRequestInterface $serverRequest): array
    {
        $path = $serverRequest->getUri()->getPath();
        $constraints = $route->getAttribute('constraints', []);
        $host = $serverRequest->getUri()->getHost();
        $routeHost = $route->getHost();
        $matches = $this->matchPath($constraints, $route->getPath(), $path);
        if ($host !== $routeHost && $routeHost !== '') {
            $matches['matched'] = false;
        }
        return $matches;
    }

    public function dispatch(array $routes, ServerRequestInterface $serverRequest): ResponseInterface
    {
        $method = $serverRequest->getMethod();
        foreach ($routes as $route) {
            $matches = $this->match($route, $serverRequest);
            if ($matches['matched']) {
                if (!in_array($method, $route->getMethods()) && !in_array('ANY', $route->getMethods())) {
                    throw new MethodNotAllowedException('Method is not allowed');
                }
                $handler = $route->getHandler();
                if (is_array($handler)) {
                    $instance = $this->container->get($handler[0]);
                    $handler = [$instance, $handler[1]];
                }
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
