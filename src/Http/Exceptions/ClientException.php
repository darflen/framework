<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Exceptions;

use Psr\Http\Client\ClientExceptionInterface;
use RuntimeException;

class ClientException extends RuntimeException implements ClientExceptionInterface {}
