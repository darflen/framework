<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Support;

use PHPUnit\Framework\TestCase;
use Darflen\Framework\Support\Arr;

class ArrTest extends TestCase
{
    public function testExpectGetOutput(): void
    {
        $input = ['fizzbuzz' => ['cat' => 'bar'], 'foo' => 0];
        $input2 = [];
        $input3 = ['fizzbuzz' => ['fizz', 'buzz', 'baz']];

        $output = Arr::from($input)->get('fizzbuzz.cat', 'failure');
        $output2 = Arr::from($input)->get('', 'failure');
        $output3 = Arr::from($input2)->get('', 'failure');
        $output4 = Arr::from($input3)->get('fizzbuzz.0', 'failure');

        $this->assertNotNull($output);
        $this->assertEquals('bar', $output);
        $this->assertNotEquals('failure', $output);
        $this->assertNotNull($output2);
        $this->assertEquals($input, $output2);
        $this->assertNotEquals('failure', $output2);
        $this->assertNotNull($output3);
        $this->assertEquals($input2, $output3);
        $this->assertNotEquals('failure', $output3);
        $this->assertNotNull($output4);
        $this->assertEquals('fizz', $output4);
        $this->assertNotEquals('failure', $output4);
    }

    public function testExpectSetOutput(): void
    {
        $input = [];

        $process = Arr::from($input)->set('cat.0', 'success 0');
        $process2 = Arr::from($input)->set('cat.1', 'success 1');
        $process3 = Arr::from($input)->set('cat.2', 'success 2');
        $process4 = Arr::from($input)->set('cat.bar', 'success bar');
        $process5 = Arr::from($input)->set('foo', 'success foo');

        $output = $process->get('cat.0', 'failure');
        $output2 = $process2->get('cat.1', 'failure');
        $output3 = $process3->get('cat.2', 'failure');
        $output4 = $process4->get('cat.bar', 'failure');
        $output5 = $process5->get('foo', 'failure');

        $this->assertEquals('success 0', $output);
        $this->assertEquals('success 1', $output2);
        $this->assertEquals('success 2', $output3);
        $this->assertEquals('success bar', $output4);
        $this->assertEquals('success foo', $output5);
    }

    public function testExpectAllOutput(): void
    {
        $input = ['input' => 'output'];

        $output = Arr::from($input)->all();

        $this->assertEquals($input, $output);
    }

    public function testExpectHasOutput(): void
    {
        $input = ['input' => 'output'];
        $input2 = ['input' => ['darf', 'foo']];

        $output = Arr::from($input)->has('input');
        $output2 = Arr::from($input)->has('failure');
        $output3 = Arr::from($input2)->has('input.0');
        $output4 = Arr::from($input2)->has('input.failure');

        $this->assertEquals(true, $output);
        $this->assertEquals(false, $output2);
        $this->assertEquals(true, $output3);
        $this->assertEquals(false, $output4);
    }
}
