<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Alpha;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AlphaRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'foobar' => true,
            'fizzbuzz' => true,
            'fizz_buzz' => false,
            'foo bar' => false,
            'foo bar baz 123' => false,
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testAlphaRule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Alpha()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
