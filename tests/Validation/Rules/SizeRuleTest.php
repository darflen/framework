<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Size;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class SizeRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'foo' => true,
            'bar' => true,
            '❤️!' => true, // Some emojis seems to be counted as 2 characters.
            'foobar' => false,
            'quux' => false,
            'bz' => false
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testSizeRule(string $item, bool $valid)
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Size(3)]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
