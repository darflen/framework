<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Container;

use Darflen\Framework\Container\Container;
use Darflen\Framework\Container\Exceptions\NotFoundException;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

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
            'Validator' => function (ContainerInterface $container) {
                return $container->get('Request');
            }
        ]);
    }

    public function testGetWithValidId(): void
    {
        $this->assertSame($this->mocksClasses['Request'], $this->container->get('Request'));
    }

    public function testGetWithInvalidId(): void
    {
        $this->expectException(NotFoundException::class);

        $this->container->get('Not here!');
    }

    public function testGetWithFunction(): void
    {
        $this->assertSame($this->mocksClasses['Request'], $this->container->get('Validator'));
    }

    public function testHasWithValidId(): void
    {
        $this->assertTrue($this->container->has('Request'));
    }

    public function testHasWithInvalidId(): void
    {
        $this->assertFalse($this->container->has('Not here!'));
    }
}
