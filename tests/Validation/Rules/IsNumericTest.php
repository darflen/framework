<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\IsNumeric;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class IsNumericTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        yield [256, true];
        yield [3.141, true];
        yield [-256, true];
        yield [sqrt(2), true];
        yield ['foobar', false];
        yield [['fizz' => 'buzz'], false];
    }

    #[DataProvider('itemDataProvider')]
    public function testIsNumericRule(mixed $item, bool $valid)
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new IsNumeric()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
