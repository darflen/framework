<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Client;

use Darflen\Framework\Http\Exceptions\ClientException;
use Darflen\Framework\Http\Exceptions\NetworkException;
use Darflen\Framework\Http\Exceptions\RequestException;
use Override;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class Client implements ClientInterface
{
    private const array AVAILABLE_CURL_PROTOCOL_VERSIONS = [
        '1' => CURL_HTTP_VERSION_1_0,
        '1.0' => CURL_HTTP_VERSION_1_0,
        '1.1' => CURL_HTTP_VERSION_1_1,
        '2' => CURL_HTTP_VERSION_2,
        '2.0' => CURL_HTTP_VERSION_2,
        '3' => CURL_HTTP_VERSION_3,
        '3.0' => CURL_HTTP_VERSION_3
    ];

    private string $userAgent;

    private int $maxTime;

    private ResponseFactoryInterface $responseFactory;

    private StreamFactoryInterface $streamFactory;

    public function __construct(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory, string $userAgent = 'Darflen/1.0 (+https://darflen.com)', int $maxTime = 10)
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->userAgent = $userAgent;
        $this->maxTime = $maxTime;
    }

    #[Override]
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        $protocolVersion = $request->getProtocolVersion();
        $rawHeaders = $request->getHeaders();
        $body = $request->getBody();
        $body->rewind();
        $size = $body->getSize();
        $body->rewind();
        $bodyResource = $body->detach();
        $userAgent = $request->getHeaderLine('User-Agent');

        $url = (string) $uri;

        $parsedHeaders = [];
        foreach ($rawHeaders as $name => $values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    $parsedHeaders[] = $name . ':' . $value;
                }
                continue;
            }
            $parsedHeaders[] = $name . ':' . $values;
        }

        $curl = curl_init();
        curl_reset($curl);

        curl_setopt($curl, CURLOPT_URL, $url);
        $curlProtocolVersion = self::AVAILABLE_CURL_PROTOCOL_VERSIONS[$protocolVersion];
        curl_setopt($curl, CURLOPT_HTTP_VERSION, $curlProtocolVersion);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->maxTime);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $parsedHeaders);

        curl_setopt($curl, CURLOPT_UPLOAD, true);
        curl_setopt($curl, CURLOPT_INFILE, $bodyResource);
        if ($size !== null) {
            curl_setopt($curl, CURLOPT_INFILESIZE, $size);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent === '' ? $this->userAgent : $userAgent);
        curl_setopt($curl, CURLOPT_ENCODING, '');

        $rawResponse = curl_exec($curl);

        $error = curl_errno($curl);
        if ($error > 0) {
            if (in_array($error, [CURLE_UNSUPPORTED_PROTOCOL, CURLE_URL_MALFORMAT], true)) {
                throw new RequestException('Request failed with code: ' . $error, $request);
            }

            if (!$rawResponse) {
                throw new NetworkException('Request failed with code: ' . $error, $request);
            }

            throw new ClientException('Request failed with code: ' . $error);
        }

        $versionCode = curl_getinfo($curl, CURLINFO_HTTP_VERSION);
        $versionCode = array_search($versionCode, self::AVAILABLE_CURL_PROTOCOL_VERSIONS, true);
        $responseCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $rawHeaders = substr($rawResponse, 0, $headerSize);
        $body = substr($rawResponse, $headerSize);

        $responseBodyStream = $this->streamFactory;
        $responseBodyStream = $responseBodyStream->createStream($body);

        $response = $this->responseFactory;
        $response = $response->createResponse($responseCode, '');
        $response = $response->withBody($responseBodyStream);
        foreach (explode("\r\n", $rawHeaders) as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $response = $response->withAddedHeader(trim($parts[0]), trim($parts[1]));
            }
        }

        $response = $response->withProtocolVersion($versionCode);

        return $response;
    }
}
