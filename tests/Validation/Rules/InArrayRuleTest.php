<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\InArray;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class InArrayRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        yield [['foo' => ['bar' => 'success']], true];
        yield [['foo' => ['buzz' => 'success']], false];
        yield [['fizz' => ['buzz' => 'success']], false];
        yield ['foobar', false];
    }

    #[DataProvider('itemDataProvider')]
    public function testInArrayRule(mixed $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new InArray('foo.bar')]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
