<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testGetters(): void
    {
        $response = new Response(200, 'OK');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getReasonPhrase());
    }

    public function testWithStatus(): void
    {
        $response = new Response(200, 'OK');

        $clone = $response->withStatus(404, 'Not Found');
        $this->assertSame(404, $clone->getStatusCode());
        $this->assertSame('Not Found', $clone->getReasonPhrase());
    }

    public function testThrowsExceptionWhenBadStatusCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Response(900, 'OK');
    }
}
