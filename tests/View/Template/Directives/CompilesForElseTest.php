<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesForElse;
use PHPUnit\Framework\TestCase;

class CompilesForElseTest extends TestCase
{
    public function testCompilesForElse(): void
    {
        $directive = new CompilesForElse();

        $input = '@forelse($inputs as $input)Foo!@emptyBar!@endforelse';
        $expected = '<?php if (!empty($inputs)): foreach ($inputs as $input): ?>Foo!<?php endforeach; else: ?>Bar!<?php endif; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
