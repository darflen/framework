<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesExtends;
use PHPUnit\Framework\TestCase;

class CompilesExtendsTest extends TestCase
{
    public function testCompilesExtends(): void
    {
        $directive = new CompilesExtends();

        $input = '@extends("fizzbuzz")';
        $expected = '<?php $this->extends = "fizzbuzz" ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
