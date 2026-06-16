<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http\Client;

use Darflen\Framework\Http\Client\Client;
use Darflen\Framework\Http\Factory\RequestFactory;
use Darflen\Framework\Http\Factory\ResponseFactory;
use Darflen\Framework\Http\Factory\StreamFactory;
use Generator;
use Override;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use PHPUnit\Framework\Attributes\DataProvider;

class ClientFeatureTest extends TestCase
{
    private ClientInterface $client;

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
        $url = 'https://httpbin.org/anything?foo=bar&fizz=buzz';
        $method = 'GET';
        $request = new RequestFactory();
        $request = $request->createRequest($method, $url);

        $response = $this->client->sendRequest($request);
        $responseBody = (string) $response->getBody();
        $body = json_decode($responseBody, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($body);
        $this->assertSame($url, $body['url'] ?? '');
        $this->assertSame($method, $body['method'] ?? '');
        $this->assertSame('buzz', $body['args']['fizz'] ?? '');
        $this->assertSame('bar', $body['args']['foo'] ?? '');
    }

    #[DataProvider('requestBasicBodyMethodsDataProvider')]
    public function testSendRequestMethodsWithBodyPayload(string $method)
    {
        $url = 'https://httpbin.org/anything';
        $payload = '{"foo": "bar"}';
        $payloadType = 'application/json';
        $request = new RequestFactory();
        $request = $request->createRequest($method, $url);
        $body = new StreamFactory();
        $body = $body->createStream($payload);
        $request = $request->withHeader('Content-Type', $payloadType);
        $request = $request->withBody($body);

        $response = $this->client->sendRequest($request);
        $responseBody = (string) $response->getBody();
        $body = json_decode($responseBody, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertNotEmpty($body);
        $this->assertNotEmpty($body['data']);
        $this->assertSame($payload, $body['data'] ?? '');
        $this->assertSame($payloadType, $body['headers']['Content-Type'] ?? '');
        $this->assertSame($url, $body['url'] ?? '');
        $this->assertSame($method, $body['method'] ?? '');
    }

    // TODO: Move to a helper class!
    private static function cartesian(array $input): Generator {
        if (!$input) { yield []; return; }
        $key = array_key_first($input);
        $remaining = array_slice($input, 1, null, true);
        foreach ($input[$key] as $val) {
            foreach (self::cartesian($remaining) as $tail) {
                yield [$key => $val] + $tail;
            }
        }
    }
}
