<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesYield;
use PHPUnit\Framework\TestCase;

class CompilesYieldTest extends TestCase
{
    public function testCompilesYield()
    {
        $directive = new CompilesYield();

        $input = '@yield("fizzbuzz")';
        $expected = '<?php echo $this->sections["fizzbuzz"] ?? \'\' ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
