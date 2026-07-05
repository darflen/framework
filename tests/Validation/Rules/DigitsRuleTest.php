<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Date;
use Darflen\Framework\Validation\Rules\Digits;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class DigitsRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            25 => false,
            1 => false,
            529 => true,
            1239 => false,
            'fizzbuzz' => false
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testDigitsRule(string $item, bool $valid)
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Digits(3)]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
