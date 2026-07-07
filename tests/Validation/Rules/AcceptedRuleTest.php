<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Accepted;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class AcceptedRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'on' => true,
            '1' => true,
            'true' => true,
            'yes' => true,
            'foobar' => false,
            '0' => false,
            'off' => false,
            'no' => false,
            'false' => false
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testAcceptedRule(string $item, bool $valid)
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Accepted()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
