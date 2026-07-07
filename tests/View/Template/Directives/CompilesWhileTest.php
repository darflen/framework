<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesWhile;
use PHPUnit\Framework\TestCase;

class CompilesWhileTest extends TestCase
{
    public function testCompilesWhile()
    {
        $directive = new CompilesWhile();

        $input = '@while(true) hello! @endwhile';
        $expected = '<?php while(true): ?> hello! <?php endwhile; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
