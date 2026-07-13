<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Translation;

use Darflen\Framework\Filesystem\Factory\FilesystemFactory;
use Darflen\Framework\Translation\Repository;
use PHPUnit\Framework\TestCase;

class RepositoryFeatureTest extends TestCase
{
    public function testLoadLocaleFileAndGet(): void
    {
        $repository = new Repository((new FilesystemFactory())->createLocalFilesystem());

        $repository->loadLocaleFile('en', __DIR__ . '/Fixtures/en/foo.json');
        $repository->loadLocaleFile('en', __DIR__ . '/Fixtures/en/bar.json');

        $this->assertSame('rab', $repository->getTranslation('en', 'oof', 'failure'));
        $this->assertSame('qux', $repository->getTranslation('en', 'fizzbuzz.fizz', 'failure'));
        $this->assertSame('success', $repository->getTranslation('en', 'fizzbuzz.quux', 'success'));
    }

    public function testLoadLocaleArray(): void
    {
        $repository = new Repository((new FilesystemFactory())->createLocalFilesystem());

        $repository->loadLocaleArray('en', ['foo' => 'bar']);
        $repository->loadLocaleArray('fr', ['fizz' => 'buzz']);

        $this->assertSame('bar', $repository->getTranslation('en', 'foo', 'failure'));
        $this->assertSame('buzz', $repository->getTranslation('fr', 'fizz', 'failure'));
    }

    public function testSet(): void
    {
        $repository = new Repository((new FilesystemFactory())->createLocalFilesystem());

        $repository->setTranslation('fr', 'foo.bar', 'foobar');

        $this->assertSame('foobar', $repository->getTranslation('fr', 'foo.bar', 'failure'));
    }
}
