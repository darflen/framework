<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\IsArray;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IsArrayRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        yield [[], true];
        yield ['fizzbuzz', false];
        yield [256, false];
    }

    #[DataProvider('itemDataProvider')]
    public function testIsArrayRule(mixed $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new IsArray()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
