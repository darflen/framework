<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Routing;

use Darflen\Framework\Http\Response;
use Darflen\Framework\Http\Factory\RequestHandlerFactory;
use Darflen\Framework\Http\Factory\ServerRequestFactory;
use Darflen\Framework\Routing\Exceptions\MethodNotAllowedException;
use Darflen\Framework\Routing\Exceptions\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Darflen\Framework\Routing\RouteCollector;
use Darflen\Framework\Routing\Router;
use Override;
use PHPUnit\Framework\TestCase;

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
        $this->router = new Router($requestHandlerFactory);
        $this->routeCollector = new RouteCollector();
        $this->routeCollector->map('GET', '/', function (): ResponseInterface {
            return new Response(200, '', [], 'Hello, World!');
        });
        $this->routeCollector->map('ANY', '/foo', function (): ResponseInterface {
            return new Response(200, '', [], 'Hello, World!');
        });
        $this->routeCollector->map('ANY', '/bar/{value}', function (): ResponseInterface {
            return new Response(200, '', [], 'Hello, World!');
        })->setConstraint('value', '[A-Za-z]+');
        $this->serverRequestFactory = new ServerRequestFactory();
    }

    public function testDispatch()
    {
        $serverRequest = $this->serverRequestFactory->createServerRequest('GET', 'https://fizzbuzz.test/');

        $response = $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);

        $this->assertSame('Hello, World!', (string) $response->getBody());
    }

    public function testDispatchThrowsExceptionWhenBadMethod()
    {
        $this->expectException(MethodNotAllowedException::class);

        $serverRequest = $this->serverRequestFactory->createServerRequest('POST', 'https://fizzbuzz.test/');

        $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);
    }

    public function testDispatchThrowsExceptionWhenNoRouteFound()
    {
        $this->expectException(NotFoundException::class);

        $serverRequest = $this->serverRequestFactory->createServerRequest('GET', 'https://fizzbuzz.test/fizzbuzz');

        $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);
    }

    public function testDispatchThrowsExceptionWhenRouteDoesNotRespectConstraint()
    {
        $this->expectException(NotFoundException::class);

        $serverRequest = $this->serverRequestFactory->createServerRequest('GET', 'https://fizzbuzz.test/bar/128');

        $this->router->dispatch($this->routeCollector->getRoutes(), $serverRequest);
    }
}
