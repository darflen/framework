<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http\Client;

use Darflen\Framework\Http\Client\Client;
use Darflen\Framework\Http\Client\Sender;
use Darflen\Framework\Http\Factory\RequestFactory;
use Darflen\Framework\Http\Factory\ResponseFactory;
use Darflen\Framework\Support\Factory\StreamFactory;
use Override;
use Generator;
use PHPUnit\Framework\TestCase;

class SenderFeatureTest extends TestCase
{
    private Sender $sender;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $requestFactory = new RequestFactory();
        $responseFactory = new ResponseFactory();
        $streamFactory = new StreamFactory();

        $client = new Client($responseFactory, $streamFactory);
        $this->sender = new Sender($client, $requestFactory, $streamFactory);
    }

    #[Override]
    protected function tearDown(): void
    {
        parent::tearDown();

        usleep(1000 * 100);
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

    public function testRequest()
    {
        $response = $this->sender->request('GET', 'http://127.0.0.1:8000/get?fizz=buzz');
        $body = (string) $response->getBody();
        $json = json_decode($body, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('args', $json);
        $this->assertSame('buzz', $json['args']['fizz']);
    }

    public function testRequestWithHeadersAndBody()
    {
        $response = $this->sender->request('POST', 'http://127.0.0.1:8000/anything', [
            'Content-Type' => 'application/json',
            'X-Fizz' => 'buzz',
            'X-Foo' => ['foo', 'bar'],
        ], '{"foo":"bar"}');
        $body = (string) $response->getBody();
        $json = json_decode($body, true);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $json);
        $this->assertNotEmpty($json['data']);
        $this->assertSame('{"foo":"bar"}', $json['data'] ?? '');
        $this->assertSame('application/json', $json['headers']['Content-Type'] ?? '');
        $this->assertSame('buzz', $json['headers']['X-Fizz'] ?? '');
        $this->assertSame('foo,bar', $json['headers']['X-Foo'] ?? '');
    }

    public function testGet()
    {
        $response = $this->sender->get('http://127.0.0.1:8000/get?foo=bar');
        $body = (string) $response->getBody();
        $json = json_decode($body, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('args', $json);
        $this->assertSame('bar', $json['args']['foo']);
    }

    public function testPost()
    {
        $response = $this->sender->post('http://127.0.0.1:8000/post', [
            'Content-Type' => 'application/json'
        ], '{"foo":"bar"}');
        $body = (string) $response->getBody();
        $json = json_decode($body, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $json);
        $this->assertNotEmpty($json['data']);
        $this->assertSame('{"foo":"bar"}', $json['data'] ?? '');
        $this->assertSame('application/json', $json['headers']['Content-Type'] ?? '');
    }

    public function testPut()
    {
        $response = $this->sender->put('http://127.0.0.1:8000/put', [
            'Content-Type' => 'application/json'
        ], '{"foo":"bar"}');
        $body = (string) $response->getBody();
        $json = json_decode($body, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $json);
        $this->assertNotEmpty($json['data']);
        $this->assertSame('{"foo":"bar"}', $json['data'] ?? '');
        $this->assertSame('application/json', $json['headers']['Content-Type'] ?? '');
    }

    public function testPatch()
    {
        $response = $this->sender->patch('http://127.0.0.1:8000/patch', [
            'Content-Type' => 'application/json'
        ], '{"foo":"bar"}');
        $body = (string) $response->getBody();
        $json = json_decode($body, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $json);
        $this->assertNotEmpty($json['data']);
        $this->assertSame('{"foo":"bar"}', $json['data'] ?? '');
        $this->assertSame('application/json', $json['headers']['Content-Type'] ?? '');
    }

    public function testDelete()
    {
        $response = $this->sender->delete('http://127.0.0.1:8000/delete?foo=bar');
        $body = (string) $response->getBody();
        $json = json_decode($body, true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('args', $json);
        $this->assertSame('bar', $json['args']['foo']);
    }
}
