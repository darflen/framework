<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template;

use Darflen\Framework\Tests\View\Template\Fixtures\CompilesAddBarSuffix;
use Darflen\Framework\View\Template\Directives\CompilesIf;
use Darflen\Framework\View\Template\Engine;
use PHPUnit\Framework\TestCase;

class EngineFeatureTest extends TestCase
{
    public function testRenderBasic()
    {
        $template = new Engine([
            new CompilesAddBarSuffix(),
            new CompilesAddBarSuffix()
        ]);

        $this->assertSame('foobarbar', $template->renderString('foo', []));
    }

    public function testRenderWithIf()
    {
        $template = new Engine([
            new CompilesIf()
        ]);

        $this->assertSame('foo!fizzbuzz', $template->renderString('foo!@if($input)foobar@elsefizzbuzz@endif', ['input' => false]));
        $this->assertSame('foo!foobar', $template->renderString('foo!@if($input)foobar@elsefizzbuzz@endif', ['input' => true]));
    }
}
