<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Uri;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Http\Factory\UriFactory;

class UriFactoryTest extends TestCase
{
    public function testUploadedFIleFactory()
    {
        $uriFactory = new UriFactory();

        $uri = $uriFactory->createUri('https://example.com');

        $this->assertSame('https://example.com', (string) $uri);
    }
}
