<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Support;

use Darflen\Framework\Support\Hash;
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

    public function testHashingAndValidation()
    {
        $hash = new Hash();
        $this->assertTrue(Hash::check('fizzbuzz', Hash::make('fizzbuzz')));
    }
}
