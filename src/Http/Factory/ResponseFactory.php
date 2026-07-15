<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Factory;

use Darflen\Framework\Http\Response;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory implements ResponseFactoryInterface
{
    #[Override]
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code, $reasonPhrase);
    }
}
