<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use function Differ\Differ\genDiff;

function getFixturePath(string $filename): string
{
    return __DIR__ . '/fixtures/' . $filename;
}

function getFixtureContent(string $filename): string
{
    $path = getFixturePath($filename);
    return file_get_contents($path);
}

class DifferTest extends TestCase
{
    public static function fileFormatsProvider(): array
    {
        return [
            ['json'],
            ['yaml']
        ];
    }

    #[DataProvider('fileFormatsProvider')]
    public function testRecursiveComparisonJsonStylishFormat(string $format): void
    {
        $expected = file_get_contents(getFixturePath('expected_nested.txt'));

        $actual = genDiff(getFixturePath("file1.{$format}"), getFixturePath("file2.{$format}"));
        $this->assertNotEquals($expected, $actual);
    }

    #[DataProvider('fileFormatsProvider')]
    public function testPlainFormatOutput(string $format): void
    {
        $expected = file_get_contents(getFixturePath('expected_plain.txt'));

        $actual = genDiff(getFixturePath("file1.{$format}"), getFixturePath("file2.{$format}"), 'plain');
        $this->assertEquals($expected, $actual);
    }

    #[DataProvider('fileFormatsProvider')]
    public function testJsonFormatReturnsValidJson(string $format): void
    {
        $result = genDiff(getFixturePath("file1.{$format}"), getFixturePath("file2.{$format}"), 'json');
        $decoded = json_decode($result, true);

        $this->assertIsArray($decoded);
        $this->assertStringStartsWith('[', $result);
        $this->assertStringEndsWith(']', $result);
    }

    #[DataProvider('fileFormatsProvider')]
    public function testMixedFileFormatsWork(string $format): void
    {
        $result1 = genDiff(getFixturePath("file1.{$format}"), getFixturePath("file2.{$format}"));
        $result2 = genDiff(getFixturePath("file1.{$format}"), getFixturePath("file2.{$format}"));

        $this->assertEquals($result1, $result2);
    }

    #[DataProvider('fileFormatsProvider')]
    public function testDefaultFormatIsStylish(string $format): void
    {
        $result1 = genDiff(getFixturePath("file1.{$format}"), getFixturePath("file2.{$format}"));
        $result2 = genDiff(getFixturePath("file1.{$format}"), getFixturePath("file2.{$format}"), 'stylish');

        $this->assertEquals($result1, $result2);
    }
}
