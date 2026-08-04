<?php

declare(strict_types=1);

namespace Darflen\Framework\Routing\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

class GenericHttpException extends RuntimeException
{
    private ServerRequestInterface $serverRequest;

    public function __construct(string $message, ServerRequestInterface $serverRequest, int $code = 0, ?Throwable $previous = null)
    {
        $this->serverRequest = $serverRequest;
        parent::__construct($message, $code, $previous);
    }

    public function getServerRequest(): ServerRequestInterface
    {
        return $this->serverRequest;
    }
}
