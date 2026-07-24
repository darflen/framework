<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Routing;

use Darflen\Framework\Container\Container;
use Darflen\Framework\Http\Factory\RequestHandlerFactory;
use Darflen\Framework\Http\Factory\ServerRequestFactory;
use Darflen\Framework\Http\Response;
use Darflen\Framework\Routing\Exceptions\MethodNotAllowedException;
use Darflen\Framework\Routing\Exceptions\NotFoundException;
use Darflen\Framework\Routing\RouteCollector;
use Darflen\Framework\Routing\Router;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class RouterFeatureTest extends TestCase
{
    private RouteCollector $routeCollector;
    private ServerRequestFactory $serverRequestFactory;
    private Router $router;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $requestHandlerFactory = new RequestHandlerFactory();
        $container = new Container([]);
        $this->router = new Router($requestHandlerFactory, $container);
        $this->routeCollector = new RouteCollector();
        $this->routeCollector->map('GET', '/', function (): ResponseInterface {
            return new Response(200, '', [], 'Hello, World!');
        });
        $this->routeCollector->map('ANY', '/foo', function (): ResponseInterface {
            return new Response(200, '', [], 'Hello, World!');
        });
        $this->routeCollector->map('ANY', '/foobar', function (): ResponseInterface {
            return new Response(200, '', [], 'Hello, Foobar!');
        })->setHost('api.fizzbuzz.test');
        $this->routeCollector->map('ANY', '/bar/{value}', function (): ResponseInterface {
            return new Response(200, '', [], 'Hello, World!');
        })->setConstraint('value', '[A-Za-z]+');
        $this->routeCollector->group('/fizz', function (RouteCollector $route) {
            $route->map('GET', '/', function (): ResponseInterface {
                return new Response(200, '', [], 'Hello, Fizz!');
            });
            $route->map('GET', '/buzz', function (): ResponseInterface {
                return new Response(200, '', [], 'Hello, Buzz!');
            });
            $this->routeCollector->group('/bazz', function (RouteCollector $route) {
                $route->map('GET', '/bazz', function (): ResponseInterface {
                    return new Response(200, '', [], 'Hello, Bazz!');
                });
            });
        }, 'api.fizzbuzz.test');
        $this->serverRequestFactory = new ServerRequestFactory();
    }

    public function testDispatch(): void
    {
        $serverRequest = $this->serverRequestFactory->createServerRequest('GET', 'https://fizzbuzz.test/');

        $response = $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);

        $this->assertSame('Hello, World!', (string) $response->getBody());
    }

    public function testDispatchWithHost(): void
    {
        $serverRequest = $this->serverRequestFactory->createServerRequest('GET', 'https://api.fizzbuzz.test/foobar');

        $response = $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);

        $this->assertSame('Hello, Foobar!', (string) $response->getBody());
    }

    public function testDispatchThrowsExceptionWithWrongHost(): void
    {
        $serverRequest = $this->serverRequestFactory->createServerRequest('GET', 'https://static.fizzbuzz.test/foobar');

        $this->expectException(NotFoundException::class);

        $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);
    }

    public function testDispatchInGroup(): void
    {
        $serverRequest = $this->serverRequestFactory->createServerRequest('GET', 'https://api.fizzbuzz.test/fizz/buzz');

        $response = $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);

        $this->assertSame('Hello, Buzz!', (string) $response->getBody());
    }

    public function testDispatchInNestedGroup(): void
    {
        $serverRequest = $this->serverRequestFactory->createServerRequest('GET', 'https://api.fizzbuzz.test/fizz/bazz/bazz');

        $response = $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);

        $this->assertSame('Hello, Bazz!', (string) $response->getBody());
    }

    public function testDispatchThrowsExceptionWhenBadMethod(): void
    {
        $this->expectException(MethodNotAllowedException::class);

        $serverRequest = $this->serverRequestFactory->createServerRequest('POST', 'https://fizzbuzz.test/');

        $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);
    }

    public function testDispatchThrowsExceptionWhenNoRouteFound(): void
    {
        $this->expectException(NotFoundException::class);

        $serverRequest = $this->serverRequestFactory->createServerRequest('GET', 'https://fizzbuzz.test/fizzbuzz');

        $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);
    }

    public function testDispatchThrowsExceptionWhenRouteDoesNotRespectConstraint(): void
    {
        $this->expectException(NotFoundException::class);

        $serverRequest = $this->serverRequestFactory->createServerRequest('GET', 'https://fizzbuzz.test/bar/128');

        $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);
    }
}
