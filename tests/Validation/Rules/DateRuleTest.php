<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Date;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DateRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'December 1st 2026' => true,
            '2026-12-01' => true,
            'foo bar baz' => false,
            '-1907/13/42' => false,
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testDateRule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Date()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
