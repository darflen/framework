<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\AlphaNum;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AlphaNumRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'foobar' => true,
            'fizzbuzz' => true,
            'fizzbuzz123' => true,
            'fizz_buzz' => false,
            'foo bar' => false,
            'foo bar baz 123' => false,
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testAlphaNumRule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new AlphaNum()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
