<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesEnv;
use PHPUnit\Framework\TestCase;

class CompilesEnvTest extends TestCase
{
    public function testCompilesEnv(): void
    {
        $directive = new CompilesEnv('prod');

        $input = '@env("prod") hello! @endenv';
        $expected = '<?php if("prod" === \'prod\' || is_array("prod") && in_array(\'prod\', "prod")): ?> hello! <?php endif; ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
