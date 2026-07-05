<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\AlphaDash;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class AlphaDashRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'foobar' => true,
            'fizzbuzz123' => true,
            'fizz_buzz' => true,
            'foo bar' => false,
            'foo bar baz 123' => false
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testAlphaDashRule(string $item, bool $valid)
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new AlphaDash()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
