<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Url;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class UrlRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'https://google.com' => true,
            'http://foo-bar-baz.com' => true,
            'http://foo-bar-baz.co.uk' => true,
            'invalid-url' => false,
            'fizzbuzz.com' => false,
            'http://[invalid-domain]' => false
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testUrlRule(string $item, bool $valid)
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Url()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
