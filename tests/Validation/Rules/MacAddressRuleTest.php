<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\MacAddress;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class MacAddressRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            '00:1A:2B:3C:4D:5E' => true,
            '00-1A-2B-3C-4D-5E' => true,
            '001A.2B3C.4D5E' => true,
            '001A2B3C4D5E' => false,
            '127.0.0.1' => false,
            '00:1A:2B:3C:4D:5E:6F' => false,
            'G0:1A:2B:3C:4D:5E' => false,
            '00-1A:2B:3C:4D-5E' => false
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testMacAddressRule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new MacAddress()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
