<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\View\Template;

use Darflen\Framework\Filesystem\Factory\FilesystemFactory;
use Darflen\Framework\Tests\View\Template\Fixtures\CompilesAddBarSuffix;
use Darflen\Framework\View\Template\Directives\CompilesFor;
use Darflen\Framework\View\Template\Directives\CompilesIf;
use Darflen\Framework\View\Template\Engine;
use PHPUnit\Framework\TestCase;

class EngineFeatureTest extends TestCase
{
    public function testRenderBasic(): void
    {
        $filesystemFactory = new FilesystemFactory();
        $filesystem = $filesystemFactory->createLocalFilesystem();

        $template = new Engine([
            new CompilesAddBarSuffix(),
            new CompilesAddBarSuffix()
        ], $filesystem);

        $this->assertSame('foobarbar', $template->renderString('foo', []));
    }

    public function testRenderWithIf(): void
    {
        $filesystemFactory = new FilesystemFactory();
        $filesystem = $filesystemFactory->createLocalFilesystem();

        $template = new Engine([
            new CompilesIf()
        ], $filesystem);

        $this->assertSame('foo!fizzbuzz', $template->renderString('foo!@if($input)foobar@elsefizzbuzz@endif', ['input' => false]));
        $this->assertSame('foo!foobar', $template->renderString('foo!@if($input)foobar@elsefizzbuzz@endif', ['input' => true]));
    }

    public function testFileRender(): void
    {
        $filesystemFactory = new FilesystemFactory();
        $filesystem = $filesystemFactory->createLocalFilesystem();

        $template = new Engine([
            new CompilesIf(),
            new CompilesFor()
        ], $filesystem);
        $result = $template->renderFile(__DIR__ . '/Fixtures/foo.php', ['bar' => 2]);
        $result = preg_replace('/\s+/', '', $result);

        $this->assertSame('BarFooBarFoo', $result);
    }
}
