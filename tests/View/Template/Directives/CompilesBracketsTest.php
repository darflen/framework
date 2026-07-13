<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesBrackets;
use PHPUnit\Framework\TestCase;

class CompilesBracketsTest extends TestCase
{
    public function testCompilesBrackets(): void
    {
        $directive = new CompilesBrackets();

        $input = '{{$foo}}';
        $expected = '<?php echo htmlspecialchars($foo, ENT_QUOTES, \'UTF-8\', false); ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
