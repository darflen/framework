<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\IsBoolean;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class IsBooleanRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'on' => true,
            'yes' => true,
            'true' => true,
            '1' => true,
            'off' => true,
            'no' => true,
            'false' => true,
            '0' => true
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
        yield[true, true];
        yield[false, true];
    }

    #[DataProvider('itemDataProvider')]
    public function testIsBooleanRule(mixed $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new IsBoolean()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
