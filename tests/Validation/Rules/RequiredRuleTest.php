<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation\Rules;

use Darflen\Framework\Validation\Rules\Required;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;

class RequiredRuleTest extends TestCase
{
    public function testRequiredRule(): void
    {
        $validator = new Validator();

        $data = [
            'foo' => 'bar',
            'fizz' => null,
            'foobar' => '',
            'fizzbuzz' => [],
        ];

        $validator->validateInputs($data, [
            'foo' => [new Required()],
            'fizz' => [new Required()],
            'foobar' => [new Required()],
            'fizzbuzz' => [new Required()],
        ]);

        $this->assertTrue(!isset($validator->getErrors()['foo']));
        $this->assertSame('Required', $validator->getErrors()['fizz'][0]);
        $this->assertSame('Required', $validator->getErrors()['foobar'][0]);
        $this->assertSame('Required', $validator->getErrors()['fizzbuzz'][0]);
    }
}
