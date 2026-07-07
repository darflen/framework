<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template;

use Darflen\Framework\Tests\View\Template\Fixtures\CompilesAddBarSuffix;
use Darflen\Framework\View\Template\Engine;
use PHPUnit\Framework\TestCase;

class EngineFeatureTest extends TestCase
{
    public function testCompile()
    {
        $template = new Engine([
            new CompilesAddBarSuffix(),
            new CompilesAddBarSuffix()
        ]);

        $this->assertSame('foobarbar', $template->compile('foo'));
    }
}
