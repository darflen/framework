<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Security;

use Darflen\Framework\Security\Hash;
use Override;
use Darflen\Framework\Config\Config;
use PHPUnit\Framework\TestCase;

class HashFeatureTest extends TestCase
{
    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $config = new Config();
        Config::setup(dirname(dirname(__DIR__)) . '/config', dirname(dirname(__DIR__)) . '/config')->create();
    }

    public function testHashingAndChecking(): void
    {
        $hash = new Hash();
        $this->assertTrue($hash->checkHash('fizzbuzz', $hash->createHash('fizzbuzz')));
    }

    public function testNeedsRehash(): void
    {
        $hash = new Hash();
        $oldHash = $hash->createHash('fizzbuzz', ['cost' => 4]);
        $this->assertTrue($hash->needsRehash($oldHash));
        $this->assertFalse($hash->needsRehash($hash->createHash('fizzbuzz')));
    }
}
