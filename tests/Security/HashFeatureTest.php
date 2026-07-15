<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Security;

use Darflen\Framework\Config\Config;
use Darflen\Framework\Security\Hash;
use Override;
use PHPUnit\Framework\TestCase;

class HashFeatureTest extends TestCase
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

    public function testHashingAndChecking(): void
    {
        $hash = new Hash(self::$config);
        $this->assertTrue($hash->checkHash('fizzbuzz', $hash->createHash('fizzbuzz')));
    }

    public function testNeedsRehash(): void
    {
        $hash = new Hash(self::$config);
        $oldHash = $hash->createHash('fizzbuzz', ['cost' => 4]);
        $this->assertTrue($hash->needsRehash($oldHash, ['cost' => 5]));
        $this->assertFalse($hash->needsRehash($hash->createHash('fizzbuzz', ['cost' => 5]), ['cost' => 5]));
    }
}
