<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Translation;

use Darflen\Framework\Filesystem\Factory\FilesystemFactory;
use Darflen\Framework\Translation\Repository;
use Darflen\Framework\Translation\Translator;
use Override;
use PHPUnit\Framework\TestCase;

class TranslatorFeatureTest extends TestCase
{
    private static Repository $repository;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $repository = new Repository((new FilesystemFactory())->createLocalFilesystem());
        $repository->loadLocaleArray('en', [
            'foo' => 'foo|bar',
            'bar' => '{1} foo|{2} bar|{3} baz|[4,12] qux|[13,*] quux',
            'baz' => 'There are :count FizzBuzz',
            'qux' => '{0} There are none|[1,*] There are :count :input',
            'quux' => 'FooBar',
            'grault' => 'FizzBuzz',
        ]);
        $repository->loadLocaleArray('fr', [
            'quux' => 'FizzBuzz',
            'thud' => 'FooBarBaz',
        ]);
        self::$repository = $repository;
    }

    public function testTranslate(): void
    {
        $translator = new Translator('fr', 'en', self::$repository);

        $this->assertSame('foo', $translator->translate('foo'));
        $this->assertSame('foo', $translator->translate('bar'));
        $this->assertSame('There are :count FizzBuzz', $translator->translate('baz'));
        $this->assertSame('There are none', $translator->translate('qux'));
        $this->assertSame('FizzBuzz', $translator->translate('quux'));
        $this->assertSame('FizzBuzz', $translator->translate('grault'));
    }

    public function testTranslatePlural(): void
    {
        $translator = new Translator('fr', 'en', self::$repository);

        $this->assertSame('foo', $translator->translatePlural('foo', 0));
        $this->assertSame('foo', $translator->translatePlural('foo', 1));
        $this->assertSame('bar', $translator->translatePlural('foo', 2));
        $this->assertSame('foo', $translator->translatePlural('bar', 1));
        $this->assertSame('bar', $translator->translatePlural('bar', 2));
        $this->assertSame('baz', $translator->translatePlural('bar', 3));
        $this->assertSame('qux', $translator->translatePlural('bar', 4));
        $this->assertSame('qux', $translator->translatePlural('bar', 8));
        $this->assertSame('qux', $translator->translatePlural('bar', 12));
        $this->assertSame('quux', $translator->translatePlural('bar', 13));
        $this->assertSame('quux', $translator->translatePlural('bar', 256));
        $this->assertSame('There are 384 FizzBuzz', $translator->translatePlural('baz', 384));
        $this->assertSame('There are none', $translator->translatePlural('qux', 0));
        $this->assertSame('There are 128 FooBar', $translator->translatePlural('qux', 128, ['input' => 'FooBar']));
        $this->assertSame('FizzBuzz', $translator->translatePlural('grault', -1));
        $this->assertSame('FizzBuzz', $translator->translatePlural('grault', 0));
        $this->assertSame('FizzBuzz', $translator->translatePlural('grault', 1));
        $this->assertSame('FizzBuzz', $translator->translatePlural('quux', 1));
    }

    public function testHasTranslate(): void
    {
        $translator = new Translator('fr', 'en', self::$repository);

        $this->assertTrue($translator->hasTranslation('foo'));
        $this->assertFalse($translator->hasTranslation('success'));
    }

    public function testGetters(): void
    {
        $translator = new Translator('fr', 'en', self::$repository);

        $this->assertSame('fr', $translator->getLocale());
        $this->assertSame('en', $translator->getFallbackLocale());
    }

    public function testSetters(): void
    {
        $translator = new Translator('fr', 'en', self::$repository);

        $translator->setlocale('en');

        $this->assertSame('en', $translator->getLocale());
        $this->assertNotSame('FooBarBaz', $translator->translate('thud'));
    }
}
