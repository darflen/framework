<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Uri;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Http\Factory\RequestFactory;
use Darflen\Framework\Http\Uri;

class RequestFactoryTest extends TestCase
{
    public function testRequestFactory()
    {
        $uri = $this->createStub(Uri::class);
        $requestFactory = new RequestFactory();

        $request = $requestFactory->createRequest('CONNECT', $uri);

        $this->assertSame('CONNECT', $request->getMethod());
        $this->assertSame($uri, $request->getUri());
    }
}
