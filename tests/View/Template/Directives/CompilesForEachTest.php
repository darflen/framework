<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesForEach;
use PHPUnit\Framework\TestCase;

class CompilesForEachTest extends TestCase
{
    public function testCompilesForEach(): void
    {
        $directive = new CompilesForEach();

        $input = '@foreach($values as $value) hello! @endforeach';
        $expected = '<?php foreach($values as $value): ?> hello! <?php endforeach; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
