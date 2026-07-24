<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\App;

use Darflen\Framework\App\App;
use Darflen\Framework\Container\Container;
use PHPUnit\Framework\TestCase;

class AppFeatureTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $container = new Container([]);
        $app = new App(__DIR__, $container);

        $app->setRouting([
            __DIR__ . '/routes',
        ]);

        $this->assertSame(__DIR__, $app->getProjectDir());
        $this->assertSame([], $app->getMiddlewares());
        $this->assertSame([__DIR__ . '/routes'], $app->getRoutes());
        $this->assertInstanceOf(App::class, $app->getInstance());
        $this->assertInstanceOf(Container::class, $app->getContainer());
    }

    public function testMiddlewareContainer(): void
    {
        $container = new Container([]);
        $app = new App(__DIR__, $container);

        $app->setMiddlewares([
            function () {
            },
            function () {
            }
        ]);
        $app->addMiddleware(function () {
        });
        $this->assertSame(3, count($app->getMiddlewares()));
    }
}
