<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Ip;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IpRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            '127.0.0.1' => true,
            '10.0.0.1' => true,
            '172.16.0.1' => true,
            '255.255.255.255' => true,
            '0.0.0.0' => true,
            '0.1.2' => false,
            '256.0.0.1' => false,
            '256.zero.zero.one' => false,
            '127.0.0.-1' => false,
            '127.000.000.001' => false,
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testIpRule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Ip()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
