<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Support;

use Darflen\Framework\Support\Crypt;
use Override;
use Darflen\Framework\Config\Config;
use PHPUnit\Framework\TestCase;

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

    public function testEncryptDecrypt()
    {
        $crypt = new Crypt();
        $this->assertSame('Fizzbuzz', Crypt::decrypt(Crypt::encrypt('Fizzbuzz')));
    }
}
