<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Min;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class MinRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'foo' => false,
            'bar' => false,
            '❤️!' => false, // Some emojis seems to be counted as 2 characters.
            'foobar' => true,
            'quux' => true,
            'fizzbuzz' => true,
            125 => true
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
        yield [[], false];
        yield [['foo', 'bar', 'baz'], false];
        yield [['foo', 'bar', 'baz', 'qux', 'quux'], true];
        yield [fn () => 0, false];
    }

    #[DataProvider('itemDataProvider')]
    public function testMinRule(mixed $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Min(4)]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
