<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesSwitch;
use PHPUnit\Framework\TestCase;

class CompilesSwitchTest extends TestCase
{
    public function testCompilesSwitch()
    {
        $directive = new CompilesSwitch();

        $input = '@switch($input)@case(1)@break@case(2)@break@endswitch';
        $expected = '<?php switch($input): ?><?php case 1: ?>@break<?php case 2: ?>@break<?php endswitch; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
