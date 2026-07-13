<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesEmpty;
use PHPUnit\Framework\TestCase;

class CompilesEmptyTest extends TestCase
{
    public function testCompilesEmpty(): void
    {
        $directive = new CompilesEmpty();

        $input = '@empty($input) hello! @endempty';
        $expected = '<?php if(empty($input)): ?> hello! <?php endif; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
