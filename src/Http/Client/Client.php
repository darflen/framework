<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Client;

use Darflen\Framework\Http\Exceptions\ClientException;
use Darflen\Framework\Http\Exceptions\NetworkException;
use Darflen\Framework\Http\Exceptions\RequestException;
use Darflen\Framework\Http\Factory\ResponseFactory;
use Darflen\Framework\Http\Factory\StreamFactory;
use Override;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface
{
    private const array AVAILABLE_CURL_PROTOCOL_VERSIONS = [
        '1.0' => CURL_HTTP_VERSION_1_0,
        '1.1' => CURL_HTTP_VERSION_1_1,
        '2' => CURL_HTTP_VERSION_2,
        '2.0' => CURL_HTTP_VERSION_2,
        '3' => CURL_HTTP_VERSION_3,
        '3.0' => CURL_HTTP_VERSION_3
    ];

    private const string USER_AGENT = 'Darflen/1.0 (+https://darflen.com)';

    #[Override]
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $curl = curl_init();

        $uri = $request->getUri();
        $method = $request->getMethod();

        $protocolVersion = $request->getProtocolVersion();
        $rawHeaders = $request->getHeaders();
        $body = $request->getBody();
        $body->rewind();
        $bodyResource = $body->detach();

        $url = (string) $uri;

        curl_setopt($curl, CURLOPT_URL, $url);
        $curlProtocolVersion = self::AVAILABLE_CURL_PROTOCOL_VERSIONS[$protocolVersion];
        curl_setopt($curl, CURLOPT_HTTP_VERSION, $curlProtocolVersion);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
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
        curl_setopt($curl, CURLOPT_HTTPHEADER, $parsedHeaders);

        curl_setopt($curl, CURLOPT_UPLOAD, true);
        curl_setopt($curl, CURLOPT_INFILE, $bodyResource);
        if ($body->getSize() !== null) {
            curl_setopt($curl, CURLOPT_INFILESIZE, $body->getSize());
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, self::USER_AGENT);

        $rawResponse = curl_exec($curl);

        $error = curl_errno($curl);
        if ($error > 0) {
            if (in_array($error, [CURLE_UNSUPPORTED_PROTOCOL, CURLE_URL_MALFORMAT], true)) {
                throw new RequestException('Request failed with code: ' . $error, $request);
            }

            if (!$rawResponse) {
                throw new NetworkException('Request failed', $request);
            }

            throw new ClientException('Request failed with code: ' . $error);
        }

        $responseCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $rawHeaders = substr($rawResponse, 0, $headerSize);
        $body = substr($rawResponse, $headerSize);

        $responseBodyStream = new StreamFactory();
        $responseBodyStream = $responseBodyStream->createStream($body);

        $response = new ResponseFactory();
        $response = $response->createResponse($responseCode, '');
        $response = $response->withBody($responseBodyStream);
        foreach (explode("\r\n", $rawHeaders) as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $response = $response->withAddedHeader(trim($parts[0]), trim($parts[1]));
            }
        }
        $versionCode = curl_getinfo($curl, CURLINFO_HTTP_VERSION);
        $versionCode = array_search($versionCode, self::AVAILABLE_CURL_PROTOCOL_VERSIONS, true);
        $response = $response->withProtocolVersion($versionCode);

        return $response;
    }
}
