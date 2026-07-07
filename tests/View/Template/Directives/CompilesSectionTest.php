<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template\Directives;

use Darflen\Framework\View\Template\Directives\CompilesSection;
use PHPUnit\Framework\TestCase;

class CompilesSectionTest extends TestCase
{
    public function testCompilesSection()
    {
        $directive = new CompilesSection();

        $input = '@section("foo")Hello!@endsection';
        $expected = '<?php ob_start(); $dSectionName = "foo" ?>Hello!<?php $this->sections[$dSectionName] ob_get_clean(); ?>';

        $this->assertSame($expected, $directive->compile($input));
    }
}
