<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\ActiveEmail;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class ActiveEmailRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'foobar@gmail.com' => true,
            'foobar@yahoo.com' => true,
            'foobar@invalid-email.co.uk' => false,
            'foobar' => false,
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testActiveEmailRule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new ActiveEmail()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
