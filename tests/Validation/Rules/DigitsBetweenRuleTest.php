<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Date;
use Darflen\Framework\Validation\Rules\Digits;
use Darflen\Framework\Validation\Rules\DigitsBetween;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class DigitsBetweenRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            25 => false,
            1 => false,
            529 => true,
            1239 => true,
            19992 => false,
            'fizzbuzz' => false
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testDigitsBetweenRule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new DigitsBetween(3, 4)]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
