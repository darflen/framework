<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View;

use Darflen\Framework\View\Template\Engine;
use Darflen\Framework\View\View;
use PHPUnit\Framework\TestCase;
use Darflen\Framework\Filesystem\Factory\FilesystemFactory;
use Darflen\Framework\View\Template\Directives\CompilesFor;
use Darflen\Framework\View\Template\Directives\CompilesIf;

class ViewFeatureTest extends TestCase
{
    public function testViewTempltae(): void
    {
        $filesystemFactory = new FilesystemFactory();
        $filesystem = $filesystemFactory->createLocalFilesystem();
        $engine = new Engine([
            new CompilesIf(),
            new CompilesFor()
        ], $filesystem);
        $view = new View($engine);

        $result = $view->viewTemplate(__DIR__ . '/Template/Fixtures/foo.php', ['bar' => 2]);

        $this->assertSame('Bar Foo Bar Foo', $result);
    }
}
