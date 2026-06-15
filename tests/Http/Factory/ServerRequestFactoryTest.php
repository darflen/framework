<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Uri;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Http\Factory\ServerRequestFactory;
use Darflen\Framework\Http\Uri;

class ServerRequestFactoryTest extends TestCase
{
    public function testServerRequestFactory()
    {
        $uri = $this->createMock(Uri::class);
        $serverRequestFactory = new ServerRequestFactory();

        $request = $serverRequestFactory->createServerRequest('POST', $uri, ['REMOTE_ADDR' => '127.0.0.1']);

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame($uri, $request->getUri());
        $this->assertSame(['REMOTE_ADDR' => '127.0.0.1'], $request->getServerParams());
    }
}
