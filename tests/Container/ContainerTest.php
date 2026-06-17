<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Container;

use Darflen\Framework\Container\Container;
use Darflen\Framework\Container\Exceptions\NotFoundException;
use Override;
use stdClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ContainerTest extends TestCase
{
    private ContainerInterface $container;

    private array $mocksClasses = [];

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $mockRequest = $this->createStub(stdClass::class);
        $this->mocksClasses = [
            'Request' => $mockRequest,
        ];

        $this->container = new Container([
            'Request' => $mockRequest,
        ]);
    }

    public function testGetWithValidId()
    {
        $this->assertSame($this->mocksClasses['Request'], $this->container->get('Request'));
    }

    public function testGetWithInvalidId()
    {
        $this->expectException(NotFoundException::class);

        $this->container->get('Not here!');
    }

    public function testHasWithValidId()
    {
        $this->assertTrue($this->container->has('Request'));
    }

    public function testHasWithInvalidId()
    {
        $this->assertFalse($this->container->has('Not here!'));
    }
}
