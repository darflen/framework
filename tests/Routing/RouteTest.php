<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Routing;

use Darflen\Framework\Routing\Route;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteTest extends TestCase
{
    public function testConstructor()
    {
        $requestHandlerMock = $this->createStub(RequestHandlerInterface::class);

        $route = new Route(['GET', 'POST'], '/foo/bar', $requestHandlerMock);

        $this->assertSame(['GET', 'POST'], $route->getMethods());
        $this->assertSame('/foo/bar', $route->getPath());
        $this->assertSame($requestHandlerMock, $route->getHandler());
    }

    public function testGettersAndSetters()
    {
        $requestHandlerMock = $this->createStub(RequestHandlerInterface::class);
        $middlewareMock1 = $this->createStub(MiddlewareInterface::class);
        $middlewareMock2 = $this->createStub(MiddlewareInterface::class);

        $route = new Route('POST', '/foo/bar', []);

        $route = $route->withMethods(['GET', 'PATCH']);
        $route = $route->withAddedMethod('POST');
        $route = $route->withPath('/fizz/buzz');
        $route = $route->withMiddlewares([$middlewareMock1]);
        $route = $route->withAddedMiddleware($middlewareMock2);
        $route = $route->withHandler($requestHandlerMock);
        $route = $route->withName('fizzbuzz');

        $this->assertSame(['GET', 'PATCH', 'POST'], $route->getMethods());
        $this->assertSame('/fizz/buzz', $route->getPath());
        $this->assertSame('fizzbuzz', $route->getName());
        $this->assertSame($requestHandlerMock, $route->getHandler());
        $this->assertSame([$middlewareMock1, $middlewareMock2], $route->getMiddlewares());
    }
}
