<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesIf;
use PHPUnit\Framework\TestCase;

class CompilesIfTest extends TestCase
{
    public function testCompilesIf(): void
    {
        $directive = new CompilesIf();

        $input = '@if($input)Foo!@elseif($input2)Bar!@elseBaz!@endif';
        $expected = '<?php if($input): ?>Foo!<?php elseif($input2): ?>Bar!<?php else: ?>Baz!<?php endif; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
