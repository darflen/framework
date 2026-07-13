<?php

declare(strict_types=1);

namespace Darflen\Framework\Tests\Support;

use PHPUnit\Framework\TestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;

class HelpersTest extends TestCase
{
    public static function fullPathDataProvider(): Generator
    {
        $data = [
            '/foo/bar' => '/foo/bar',
            '/foo/./bar' => '/foo/bar',
            '/foo/../bar' => '/bar',
            'foo/bar' => 'foo/bar',
            'foo/./bar' => 'foo/bar',
            'foo/../bar' => 'bar',
            '/foo/fizzbuzz.php' => '/foo/fizzbuzz.php',
            '/foo/../fizzbuzz.php' => '/fizzbuzz.php',
            'foo/fizzbuzz.php' => 'foo/fizzbuzz.php',
            'foo/../fizzbuzz.php' => 'fizzbuzz.php',
            'foo/./bar/baz/../foobar.php' => 'foo/bar/foobar.php',
            'C:/foo\././.\../fizzbuzz.php' => 'C:/fizzbuzz.php',
            '/../../../../foo/bar' => '/foo/bar',
            'foo///bar\\\\foobar.php' => 'foo/bar/foobar.php',
            'D:\\foo\\bar' => 'D:/foo/bar'
        ];

        foreach ($data as $input => $expected) {
            yield [$input, $expected];
        }
    }

    #[DataProvider('fullPathDataProvider')]
    public function testNormalizePathWithDefaultMode(string $input, string $expected)
    {
        $this->assertSame($expected, normalizePath($input));
    }

    public function testBase64JsonEncodingAndDecoding()
    {
        $this->assertSame(['foo' => 'bar'], jsonDecodeBase64(jsonEncodeBase64(['foo' => 'bar'])));
    }
}
