<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Http\Factory;

use Darflen\Framework\Http\Factory\UriFactory;
use PHPUnit\Framework\TestCase;

class UriFactoryTest extends TestCase
{
    public function testUploadedFileFactory(): void
    {
        $uriFactory = new UriFactory();

        $uri = $uriFactory->createUri('https://example.com');

        $this->assertSame('https://example.com', (string) $uri);
    }
}
