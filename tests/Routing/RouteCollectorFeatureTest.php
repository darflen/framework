<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Routing;

use Darflen\Framework\Routing\RouteCollector;
use PHPUnit\Framework\TestCase;

class RouteCollectorFeatureTest extends TestCase
{
    public function testMap(): void
    {
        $routeCollector = new RouteCollector();

        $routeCollector->map('GET', '/fizzbuzz', fn () => 'foobar');
        $routeCollector->map(['POST', 'PATCH'], '/foobar', fn () => 'fizzbuzz');
        $routes = $routeCollector->getRoutes();

        $this->assertSame(['GET'], $routes[0]->getMethods());
        $this->assertSame('/fizzbuzz', $routes[0]->getPath());
        $this->assertSame('foobar', $routes[0]->getHandler()());
        $this->assertSame(['POST', 'PATCH'], $routes[1]->getMethods());
    }

    public function testMapFluent(): void
    {
        $routeCollector = new RouteCollector();

        $routeCollector->map('GET', '/fizzbuzz', fn () => 'foobar')->addMiddlewares([fn () => 'fizzbuzz'])->addMiddleware(fn () => 'foobar')->setName('foobarfizzbuzz')->setHost('api.foobar.com');
        $routes = $routeCollector->getRoutes();

        $this->assertSame('foobarfizzbuzz', $routes[0]->getName());
        $this->assertSame('api.foobar.com', $routes[0]->GetHost());
        $this->assertSame('fizzbuzz', $routes[0]->getMiddlewares()[0]());
        $this->assertSame('foobar', $routes[0]->getMiddlewares()[1]());
    }

    public function testHelpers(): void
    {
        $routeCollector = new RouteCollector();

        $routeCollector->get('/fizzbuzz', fn () => 'foobar');
        $routeCollector->post('/fizzbuzz', fn () => 'foobar');
        $routeCollector->put('/fizzbuzz', fn () => 'foobar');
        $routeCollector->patch('/fizzbuzz', fn () => 'foobar');
        $routeCollector->delete('/fizzbuzz', fn () => 'foobar');
        $routeCollector->any('/fizzbuzz', fn () => 'foobar');

        $routes = $routeCollector->getRoutes();

        $this->assertSame(['GET'], $routes[0]->getMethods());
        $this->assertSame(['POST'], $routes[1]->getMethods());
        $this->assertSame(['PUT'], $routes[2]->getMethods());
        $this->assertSame(['PATCH'], $routes[3]->getMethods());
        $this->assertSame(['DELETE'], $routes[4]->getMethods());
        $this->assertSame(['ANY'], $routes[5]->getMethods());
    }
}
