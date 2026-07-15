<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\IsInteger;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IsIntegerRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        yield [256, true];
        yield [3.141, false];
        yield [-256, true];
        yield [sqrt(2), false];
        yield ['foobar', false];
        yield [['fizz' => 'buzz'], false];
    }

    #[DataProvider('itemDataProvider')]
    public function testIsIntegerRule(mixed $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new IsInteger()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
