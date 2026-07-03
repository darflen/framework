<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http\Client;

use Darflen\Framework\Http\Client\Client;
use Darflen\Framework\Http\Exceptions\NetworkException;
use Darflen\Framework\Http\Factory\RequestFactory;
use Darflen\Framework\Http\Factory\ResponseFactory;
use Darflen\Framework\Support\Factory\StreamFactory;
use Darflen\Framework\Support\Arr;
use Generator;
use Override;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ClientFeatureTest extends TestCase
{
    private Client $client;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();

        $streamFactory = new StreamFactory();
        $responseFactory = new ResponseFactory();
        $this->client = new Client($responseFactory, $streamFactory);
    }

    #[Override]
    public function tearDown(): void
    {
        parent::tearDown();

        usleep(1000 * 25);
    }

    public static function requestBasicDataProvider(): Generator
    {
        $data = [
            'method' => ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'],
            'path' => ['/status'],
            'code' => [200, 301, 302, 400, 500]
        ];

        foreach (Arr::cartesian($data) as $combo) {
            yield [$combo];
        }
    }

    public static function requestBasicBodyMethodsDataProvider(): Generator
    {
        $data = [
            'POST',
            'PATCH',
            'PUT'
        ];

        foreach ($data as $item) {
            yield [$item];
        }
    }

    public static function requestBasicDataEncodedDataProvider(): Generator
    {
        $data = [
            'brotli',
            'gzip',
            'deflate'
        ];

        foreach ($data as $item) {
            yield [$item];
        }
    }

    #[DataProvider('requestBasicDataProvider')]
    public function testSendRequestStatus(array $combo): void
    {
        $request = new RequestFactory();
        $request = $request->createRequest($combo['method'], 'http://127.0.0.1:8000' . $combo['path'] . '/' . $combo['code']);

        $response = $this->client->sendRequest($request);

        $this->assertSame($combo['code'], $response->getStatusCode());
    }

    public function testSendRequestHeaders(): void
    {
        $request = new RequestFactory();
        $request = $request->createRequest('GET', 'http://127.0.0.1:8000/headers');

        $response = $this->client->sendRequest($request);
        $responseBody = (string) $response->getBody();
        $headers = json_decode($responseBody, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getHeaders());
        $this->assertNotEmpty($headers);
        $this->assertArrayHasKey('headers', $headers);
    }

    public function testSendRequestGoodJsonBody(): void
    {
        $request = new RequestFactory();
        $request = $request->createRequest('GET', 'http://127.0.0.1:8000/json');

        $response = $this->client->sendRequest($request);
        $responseBody = (string) $response->getBody();
        $body = json_decode($responseBody, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($body);
        $this->assertArrayHasKey('slideshow', $body);
        $this->assertSame('Why <em>WonderWidgets</em> are great', $body['slideshow']['slides'][1]['items'][0] ?? '');
    }

    public function testSendRequestGet()
    {
        $request = new RequestFactory();
        $request = $request->createRequest('GET', 'http://127.0.0.1:8000/anything?foo=bar&fizz=buzz');

        $response = $this->client->sendRequest($request);
        $responseBody = (string) $response->getBody();
        $body = json_decode($responseBody, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($body);
        $this->assertSame('GET', $body['method'] ?? '');
        $this->assertSame('buzz', $body['args']['fizz'] ?? '');
        $this->assertSame('bar', $body['args']['foo'] ?? '');
    }

    #[DataProvider('requestBasicBodyMethodsDataProvider')]
    public function testSendRequestMethodsWithBodyPayload(string $method)
    {
        $request = new RequestFactory();
        $request = $request->createRequest($method, 'http://127.0.0.1:8000/anything');
        $body = new StreamFactory();
        $body = $body->createStream('{"foo": "bar"}');
        $request = $request->withHeader('Content-Type', 'application/json');
        $request = $request->withBody($body);

        $response = $this->client->sendRequest($request);
        $responseBody = (string) $response->getBody();
        $body = json_decode($responseBody, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($body);
        $this->assertNotEmpty($body['data']);
        $this->assertSame('{"foo": "bar"}', $body['data'] ?? '');
        $this->assertSame('application/json', $body['headers']['Content-Type'] ?? '');
        $this->assertSame($method, $body['method'] ?? '');
    }

    public function testSendRequestGetInvalidUrl()
    {
        $this->expectException(NetworkException::class);

        $request = new RequestFactory();
        $request = $request->createRequest('GET', 'https://1234invalidurl.net');

        $this->client->sendRequest($request);
    }

    #[DataProvider('requestBasicDataEncodedDataProvider')]
    public function testSendRequestGoodEncodedBody(string $encoding): void
    {
        $request = new RequestFactory();
        $request = $request->createRequest('GET', 'http://127.0.0.1:8000/' . $encoding);

        $response = $this->client->sendRequest($request);
        $responseBody = (string) $response->getBody();
        $body = json_decode($responseBody, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($body);
        $this->assertArrayHasKey('headers', $body);
    }

    public function testSendRequestGoodUtf8Body(): void
    {
        $request = new RequestFactory();
        $request = $request->createRequest('GET', 'http://127.0.0.1:8000/encoding/utf8');

        $response = $this->client->sendRequest($request);
        $responseBody = (string) $response->getBody();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($responseBody);
        $this->assertStringContainsString('⠧', $responseBody);
    }
}
