<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Support;

use Darflen\Framework\App\App;
use Darflen\Framework\Container\Container;
use Darflen\Framework\Filesystem\Factory\FilesystemFactory;
use Darflen\Framework\Translation\Repository;
use Darflen\Framework\Translation\Translator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public static function fullPathDataProvider(): Generator
    {
        $data = [
            '/foo/bar' => '/foo/bar',
            '/foo/./bar' => '/foo/bar',
            '/foo/../bar' => '/bar',
            'foo/bar' => 'foo/bar',
            'foo/./bar' => 'foo/bar',
            'foo/../bar' => 'bar',
            '/foo/fizzbuzz.php' => '/foo/fizzbuzz.php',
            '/foo/../fizzbuzz.php' => '/fizzbuzz.php',
            'foo/fizzbuzz.php' => 'foo/fizzbuzz.php',
            'foo/../fizzbuzz.php' => 'fizzbuzz.php',
            'foo/./bar/baz/../foobar.php' => 'foo/bar/foobar.php',
            'C:/foo\././.\../fizzbuzz.php' => 'C:/fizzbuzz.php',
            '/../../../../foo/bar' => '/foo/bar',
            'foo///bar\\\\foobar.php' => 'foo/bar/foobar.php',
            'D:\\foo\\bar' => 'D:/foo/bar',
        ];

        foreach ($data as $input => $expected) {
            yield [$input, $expected];
        }
    }

    #[DataProvider('fullPathDataProvider')]
    public function testNormalizePathWithDefaultMode(string $input, string $expected): void
    {
        $this->assertSame($expected, normalizePath($input));
    }

    public function testBase64JsonEncodingAndDecoding(): void
    {
        $this->assertSame(['foo' => 'bar'], jsonDecodeBase64(jsonEncodeBase64(['foo' => 'bar'])));
    }

    public function testContainer(): void
    {
        $container = new Container([]);
        new App(__DIR__, $container);

        $this->assertInstanceOf(Container::class, container());
    }

    public function testTrans(): void
    {
        $repository = new Repository((new FilesystemFactory())->createLocalFilesystem());
        $repository->loadLocaleArray('en', ['foo' => 'fizzbuzz']);
        $repository->loadLocaleArray('fr', ['foo' => 'foobar']);
        $container = new Container([
            Translator::class => new Translator('fr', 'en', $repository),
        ]);
        new App(__DIR__, $container);

        $this->assertSame('foobar', trans('foo', []));
    }

    public function testTransPlural(): void
    {
        $repository = new Repository((new FilesystemFactory())->createLocalFilesystem());
        $repository->loadLocaleArray('en', ['foo' => 'fizzbuzz|fizzbuzzbazz']);
        $repository->loadLocaleArray('fr', ['foo' => 'foobar|foobarbaz']);
        $container = new Container([
            Translator::class => new Translator('fr', 'en', $repository),
        ]);
        new App(__DIR__, $container);

        $this->assertSame('foobar', transPlural('foo', 1, []));
        $this->assertSame('foobarbaz', transPlural('foo', 2, []));
    }

    public function test__(): void
    {
        $repository = new Repository((new FilesystemFactory())->createLocalFilesystem());
        $repository->loadLocaleArray('en', ['foo' => 'fizzbuzz']);
        $repository->loadLocaleArray('fr', ['foo' => 'foobar']);
        $container = new Container([
            Translator::class => new Translator('fr', 'en', $repository),
        ]);
        new App(__DIR__, $container);

        $this->assertSame('foobar', __('foo', []));
    }

    public function test___(): void
    {
        $repository = new Repository((new FilesystemFactory())->createLocalFilesystem());
        $repository->loadLocaleArray('en', ['foo' => 'fizzbuzz|fizzbuzzbazz']);
        $repository->loadLocaleArray('fr', ['foo' => 'foobar|foobarbaz']);
        $container = new Container([
            Translator::class => new Translator('fr', 'en', $repository),
        ]);
        new App(__DIR__, $container);

        $this->assertSame('foobar', ___('foo', 1, []));
        $this->assertSame('foobarbaz', ___('foo', 2, []));
    }

    public function testUniqueId(): void
    {
        $this->assertSame(24, strlen(uniqueId()));
        $this->assertSame(48, strlen(uniqueId(24)));
    }
}
