<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Support;

use Darflen\Framework\Support\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    public function testSwrap(): void
    {
        $result = Str::swrap('The quick brown {foo} jumps over the lazy {bar}', ['{foo}' => 'fox', '{bar}' => 'dog']);

        $this->assertSame('The quick brown fox jumps over the lazy dog', $result);
    }
}
