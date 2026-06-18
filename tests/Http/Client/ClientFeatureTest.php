<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http\Client;

use Darflen\Framework\Http\Client\Client;
use Darflen\Framework\Http\Exceptions\NetworkException;
use Darflen\Framework\Http\Factory\RequestFactory;
use Darflen\Framework\Http\Factory\ResponseFactory;
use Darflen\Framework\Http\Factory\StreamFactory;
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
    protected function tearDown(): void
    {
        parent::tearDown();

        usleep(1000 * 300);
    }

    public static function requestBasicDataProvider(): Generator
    {
        $data = [
            'method' => ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'],
            'path' => ['/status'],
            'code' => [200, 301, 302, 400, 500]
        ];

        foreach (self::cartesian($data) as $combo) {
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
        $request = $request->createRequest($combo['method'], 'https://httpbin.org' . $combo['path'] . '/' . $combo['code']);

        $response = $this->client->sendRequest($request);

        $this->assertSame($combo['code'], $response->getStatusCode());
    }

    public function testSendRequestHeaders(): void
    {
        $request = new RequestFactory();
        $request = $request->createRequest('GET', 'https://httpbin.org/headers');

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
        $request = $request->createRequest('GET', 'https://httpbin.org/json');

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
        $request = $request->createRequest('GET', 'https://httpbin.org/anything?foo=bar&fizz=buzz');

        $response = $this->client->sendRequest($request);
        $responseBody = (string) $response->getBody();
        $body = json_decode($responseBody, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($body);
        $this->assertSame('https://httpbin.org/anything?foo=bar&fizz=buzz', $body['url'] ?? '');
        $this->assertSame('GET', $body['method'] ?? '');
        $this->assertSame('buzz', $body['args']['fizz'] ?? '');
        $this->assertSame('bar', $body['args']['foo'] ?? '');
    }

    #[DataProvider('requestBasicBodyMethodsDataProvider')]
    public function testSendRequestMethodsWithBodyPayload(string $method)
    {
        $request = new RequestFactory();
        $request = $request->createRequest($method, 'https://httpbin.org/anything');
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
        $this->assertSame('https://httpbin.org/anything', $body['url'] ?? '');
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
        $request = $request->createRequest('GET', 'https://httpbin.org/' . $encoding);

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
        $request = $request->createRequest('GET', 'https://httpbin.org/encoding/utf8');

        $response = $this->client->sendRequest($request);
        $responseBody = (string) $response->getBody();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($responseBody);
        $this->assertStringContainsString('⠧', $responseBody);
    }

    // TODO: Move to a helper class!
    private static function cartesian(array $input): Generator
    {
        if (!$input) {
            yield [];
            return;
        }
        $key = array_key_first($input);
        $remaining = array_slice($input, 1, null, true);
        foreach ($input[$key] as $val) {
            foreach (self::cartesian($remaining) as $tail) {
                yield [$key => $val] + $tail;
            }
        }
    }
}
