<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\IsFile;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IsFileRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        yield [__DIR__ . '/../Fixtures/EqualsTo.php', true];
        yield ['/', false];
        yield [__DIR__, false];
    }

    #[DataProvider('itemDataProvider')]
    public function testIsFileRule(mixed $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new IsFile()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
