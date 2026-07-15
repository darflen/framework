<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http\Factory;

use Darflen\Framework\Http\Factory\ResponseFactory;
use PHPUnit\Framework\TestCase;

class ResponseFactoryTest extends TestCase
{
    public function testResponseFactory(): void
    {
        $responseFactory = new ResponseFactory();

        $response = $responseFactory->createResponse(400, 'Bad Request');

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Bad Request', $response->getReasonPhrase());
    }
}
