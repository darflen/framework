<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http;

use Darflen\Framework\Http\RequestHandler;
use Darflen\Framework\Http\Response;
use Darflen\Framework\Http\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandlerFeatureTest extends TestCase
{
    public function testHandleWithCallables()
    {
        $requestHandler = new RequestHandler([
            function (ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface {
                $request = $request->withHeader('X-Went-To-Middleware', 'foo');
                return $next->handle($request);
            },
            function (ServerRequestInterface $request): ResponseInterface {
                return new Response(200, '', [
                    'X-Went-To-Middleware' => $request->getHeaderLine('X-Went-To-Middleware')
                ]);
            }
        ]);
        $request = new ServerRequest('GET', 'https://localhost');
        $response = $requestHandler->handle($request);

        $this->assertSame('foo', $response->getHeaderLine('X-Went-To-Middleware'));
    }
}
