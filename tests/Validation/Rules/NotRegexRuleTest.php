<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\NotRegex;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class NotRegexRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            '0A2F' => false,
            'F155BC55' => false,
            'foobar' => true,
            'fizzbuzz' => true
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testNotRegexRule(string $item, bool $valid)
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new NotRegex('/[0-9A-F]/')]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
