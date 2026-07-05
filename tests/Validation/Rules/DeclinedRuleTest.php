<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Declined;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class DeclinedRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'on' => false,
            '1' => false,
            'true' => false,
            'yes' => false,
            'foobar' => false,
            '0' => true,
            'off' => true,
            'no' => true,
            'false' => true
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testDeclinedRuleTest(string $item, bool $valid)
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Declined()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
