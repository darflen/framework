<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\IsString;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class IsStringRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        yield ['foobar', true];
        yield [256, false];
        yield [['foo' => 'bar'], false];
    }

    #[DataProvider('itemDataProvider')]
    public function testIsStringRule(mixed $item, bool $valid)
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new IsString()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
