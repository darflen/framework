<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesIsset;
use PHPUnit\Framework\TestCase;

class CompilesIssetTest extends TestCase
{
    public function testCompilesIsset(): void
    {
        $directive = new CompilesIsset();

        $input = '@isset($input) hello! @endisset';
        $expected = '<?php if(isset($input)): ?> hello! <?php endif; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
