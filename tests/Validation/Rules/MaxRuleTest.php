<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Max;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class MaxRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'foo' => true,
            'bar' => true,
            '❤️!' => true, // Some emojis seems to be counted as 2 characters.
            'foobar' => false,
            'quux' => false,
            'fizzbuzz' => false,
            125 => false
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
        yield [[], true];
        yield [['foo', 'bar', 'baz'], true];
        yield [['foo', 'bar', 'baz', 'qux', 'quux'], false];
        yield [fn () => 0, false];
    }

    #[DataProvider('itemDataProvider')]
    public function testMaxRule(mixed $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Max(3)]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
