<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Between;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BetweenRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'foo' => true,
            'bar' => true,
            'quux' => true,
            'foobar' => false,
            'foobarbaz' => false,
            'fizzbuzz' => false,
            125 => false,
            4 => true,
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
        yield[['foo', 'bar', 'baz'], true];
        yield[['foo', 'bar', 'baz', 'qux', 'quux'], false];
        yield[fn () => 0, false];
    }

    #[DataProvider('itemDataProvider')]
    public function testBetweenRule(mixed $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Between(3, 4)]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
