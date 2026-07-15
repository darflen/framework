<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\ActiveUrl;
use Darflen\Framework\Validation\Validator;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ActiveUrlRuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            'https://google.com' => true,
            'https://www.dagon-1.net' => true,
            'https://foo-bar-baz-invalid-domain.com' => false,
            'foobar.com' => false,
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testActiveUrlRule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new ActiveUrl()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
