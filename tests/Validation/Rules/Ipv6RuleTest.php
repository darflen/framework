<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Ipv6;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class Ipv6RuleTest extends TestCase
{
    public static function itemDataProvider(): Generator
    {
        $data = [
            '127.0.0.1' => false,
            '10.0.0.1' => false,
            '0.0.0.0' => false,
            '2001:db8:3333:4444:5555:6666:7777:8888' => true,
            '2000::' => true,
            '::' => true,
            '2001:db8:0:0:0:ff00:42:8329' => true,
            '2001:db8:3333:4444:5555:6666:7777:8888:9999' => false,
            '2001:db8:3333:4444:5555:6666:7777:8888::' => false
        ];

        foreach ($data as $item => $valid) {
            yield [$item, $valid];
        }
    }

    #[DataProvider('itemDataProvider')]
    public function testIpv6Rule(string $item, bool $valid): void
    {
        $validator = new Validator();

        $validator->validateInputs(['foo' => $item], ['foo' => [new Ipv6()]]);

        $this->assertSame($valid, $validator->didPass());
    }
}
