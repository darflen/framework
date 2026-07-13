<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Regex;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class RegexRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            '0A2F' => true,
            'F155BC55' => true,
            'foobar' => false,
            'fizzbuzz' => false
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testRegexRule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Regex('/[0-9A-F]/')]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
