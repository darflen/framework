<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Security;

use Darflen\Framework\Security\Crypt;
use Override;
use Darflen\Framework\Config\Config;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CryptFeatureTest extends TestCase
{
    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $config = new Config();
        Config::setup(dirname(dirname(__DIR__)) . '/config', dirname(dirname(__DIR__)) . '/config')->create();
        Config::set('security.encryption.key', '[d595ami5@9]uFZAPe/{4*iUGLkk,Zxr');
    }

    public function testEncryptDecrypt(): void
    {
        $crypt = new Crypt();
        $this->assertSame('Fizzbuzz', $crypt->decryptValue($crypt->encryptValue('Fizzbuzz')));
    }

    public function testEncryptDecryptWithBadValue(): void
    {
        $this->expectException(RuntimeException::class);

        $crypt = new Crypt();
        $crypt->decryptValue(strrev($crypt->encryptValue('Fizzbuzz')));
    }
}
