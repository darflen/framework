<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Ascii;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AsciiRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'foobar' => true,
            'fizzbuzz!?@#$%^&*' => true,
            "foo\tbar\n" => true,
            'fizz and buzz' => true,
            '❤️❤️❤️' => false,
            "foo\xA0bar" => false,
            'ほげ' => false,
            'café' => false,
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testAsciiRule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Ascii()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
