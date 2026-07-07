<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesProduction;
use PHPUnit\Framework\TestCase;

class CompilesProductionTest extends TestCase
{
    public function testCompilesProduction()
    {
        $directive = new CompilesProduction('prod');

        $input = '@production hello! @endproduction';
        $expected = '<?php if(\'prod\' === \'prod\'): ?> hello! <?php endif; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
