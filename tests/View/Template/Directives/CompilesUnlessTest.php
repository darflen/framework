<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesUnless;
use PHPUnit\Framework\TestCase;

class CompilesUnlessTest extends TestCase
{
    public function testCompilesUnless(): void
    {
        $directive = new CompilesUnless();

        $input = '@unless($input) hello! @endunless';
        $expected = '<?php if(!$input): ?> hello! <?php endif; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
