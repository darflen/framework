<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\Response;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class ResponseTest extends TestCase
{
    public function testGetters()
    {
        $response = new Response(200, 'OK');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getReasonPhrase());
    }

    public function testWithStatus()
    {
        $response = new Response(200, 'OK');

        $clone = $response->withStatus(404, 'Not Found');
        $this->assertSame(404, $clone->getStatusCode());
        $this->assertSame('Not Found', $clone->getReasonPhrase());
    }

    public function testThrowsExceptionWhenBadStatusCode()
    {
        $this->expectException(InvalidArgumentException::class);

        new Response(900, 'OK');
    }
}
