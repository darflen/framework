<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\NotInArray;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NotInArrayRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        yield [['foo' => ['bar' => 'success']], false];
        yield [['foo' => ['buzz' => 'success']], true];
        yield [['fizz' => ['buzz' => 'success']], true];
        yield ['foobar', false];
    }

    #[DataProvider('itemDataProvider')]
    public function testNotInArrayRule(mixed $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new NotInArray('foo.bar')]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
