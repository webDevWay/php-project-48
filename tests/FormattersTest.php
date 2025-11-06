<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Formatters\format;

class FormattersTest extends TestCase
{
    public function testFormatFunctionReturnsStylishByDefault(): void
    {
        $diff = [
            [
                'key' => 'test',
                'type' => 'added',
                'value' => 'value'
            ]
        ];

        $result = format($diff, 'stylish');

        $this->assertStringContainsString('+ test: value', $result);
        $this->assertStringStartsWith('{', $result);
        $this->assertStringEndsWith('}', $result);
    }

    public function testFormatFunctionReturnsPlainFormat(): void
    {
        $diff = [
            [
                'key' => 'test',
                'type' => 'added',
                'value' => 'value'
            ]
        ];

        $result = format($diff, 'plain');

        $this->assertStringContainsString("Property 'test' was added with value: 'value'", $result);
    }

    public function testFormatFunctionReturnsJsonFormat(): void
    {
        $diff = [
            [
                'key' => 'test',
                'type' => 'added',
                'value' => 'value'
            ]
        ];

        $result = format($diff, 'json');
        $decoded = json_decode($result, true);

        $this->assertEquals($diff, $decoded);
    }

    public function testFormatFunctionThrowsExceptionForUnknownFormat(): void
    {
        $diff = [];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown format: unknown');

        format($diff, 'unknown');
    }
}
