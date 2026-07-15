<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Json;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class JsonRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            json_encode(['foo' => 'bar']) => true,
            '{{}}' => false,
            '{' => false,
            '{"foo"=>"bar"}' => false,
        ];

        foreach ($data as $item => $valid) {
            yield [(string) $item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testJsonRule(mixed $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Json()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
