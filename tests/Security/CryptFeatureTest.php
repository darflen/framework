<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Security;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Security\Crypt;
use Override;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class CryptFeatureTest extends TestCase
{
    private static Config $config;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $config = new Config();
        $config->loadConfigDirectory(dirname(__DIR__, 2) . '/config');
        $config->set('security.encryption.key', '[d595ami5@9]uFZAPe/{4*iUGLkk,Zxr');
        self::$config = $config;
    }

    public function testEncryptDecrypt(): void
    {
        $crypt = new Crypt(self::$config);
        $this->assertSame('Fizzbuzz', $crypt->decryptValue($crypt->encryptValue('Fizzbuzz')));
    }

    public function testEncryptDecryptWithBadValue(): void
    {
        $this->expectException(RuntimeException::class);

        $crypt = new Crypt(self::$config);
        $crypt->decryptValue(strrev($crypt->encryptValue('Fizzbuzz')));
    }
}
