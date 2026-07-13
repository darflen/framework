<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesFor;
use PHPUnit\Framework\TestCase;

class CompilesForTest extends TestCase
{
    public function testCompilesFor(): void
    {
        $directive = new CompilesFor();

        $input = '@for(int $index = 0; $index < 10; $index++) hello! @endfor';
        $expected = '<?php for(int $index = 0; $index < 10; $index++): ?> hello! <?php endfor; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
