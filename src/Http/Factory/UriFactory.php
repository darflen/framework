<?php

declare(strict_types=1);

namespace Darflen\Framework\Http\Factory;

use Override;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Darflen\Framework\Http\Uri;

class UriFactory implements UriFactoryInterface
{
    #[Override]
    public function createUri(string $uri = ''): UriInterface
    {
        return new Uri($uri);
    }
}
