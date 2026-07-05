<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Validation;

use Darflen\Framework\Tests\Validation\Fixtures\EqualsTo;
use Darflen\Framework\Validation\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorFeatureTest extends TestCase
{
    public function testValidateInputsInGoodSituation(): void
    {
        $validator = new Validator();
        $data = [
            'number' => '25',
            'string' => 'foo'
        ];

        $validator->validateInputs($data, [
            'number' => [new EqualsTo('25')]
        ]);

        $this->assertTrue($validator->didPass());
        $this->assertFalse($validator->didFail());
        $this->assertSame([], $validator->getErrors());
    }

    public function testValidateInputsInBadSituation(): void
    {
        $validator = new Validator();
        $data = [
            'number' => '256',
            'string' => 'foo'
        ];

        $validator->validateInputs($data, [
            'number' => [new EqualsTo('25')]
        ]);

        $this->assertFalse($validator->didPass());
        $this->assertTrue($validator->didFail());
        $this->assertSame(['number' => ['EqualsTo']], $validator->getErrors());
    }

    public function testValidateInputsInNonExistentInput()
    {
        $validator = new Validator();
        $data = [
            'string' => 'foo'
        ];

        $validator->validateInputs($data, [
            'number' => [new EqualsTo('25')]
        ]);

        $this->assertFalse($validator->didPass());
        $this->assertTrue($validator->didFail());
        $this->assertSame(['number' => ['EqualsTo']], $validator->getErrors());
    }
}
