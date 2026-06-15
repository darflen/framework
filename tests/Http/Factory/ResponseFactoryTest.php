<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Uri;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Http\Factory\ResponseFactory;

class ResponseFactoryTest extends TestCase
{
    public function testResponseFactory()
    {
        $responseFactory = new ResponseFactory();

        $response = $responseFactory->createResponse(400, 'Bad Request');

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Bad Request', $response->getReasonPhrase());
    }
}
