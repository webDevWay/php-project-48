<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function Differ\Differ\buildDiff;
use function Differ\Differ\getFileFormat;

class DifferTest extends TestCase
{
    public function testRecursiveComparisonJsonStylishFormat(): void
    {
        $expected = file_get_contents('tests/fixtures/expected_nested.txt');

        $actual = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
        $this->assertNotEquals($expected, $actual);
    }

    public function testPlainFormatOutput(): void
    {
        $expected = file_get_contents('tests/fixtures/expected_plain.txt');

        $actual = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'plain');
        $this->assertEquals($expected, $actual);
    }

    public function testJsonFormatReturnsValidJson(): void
    {
        $result = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'json');
        $decoded = json_decode($result, true);

        $this->assertIsArray($decoded);
        $this->assertStringStartsWith('[', $result);
        $this->assertStringEndsWith(']', $result);
    }

    public function testMixedFileFormatsWork(): void
    {
        $result1 = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.yaml');
        $result2 = genDiff('tests/fixtures/file1.yaml', 'tests/fixtures/file2.json');

        $this->assertEquals($result1, $result2);
    }

    public function testDefaultFormatIsStylish(): void
    {
        $result1 = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json');
        $result2 = genDiff('tests/fixtures/file1.json', 'tests/fixtures/file2.json', 'stylish');

        $this->assertEquals($result1, $result2);
    }

    public function testBuildDiffWithFlatObjects(): void
    {
        $obj1 = (object) ['a' => 1, 'b' => 2];
        $obj2 = (object) ['a' => 1, 'c' => 3];

        $diff = buildDiff($obj1, $obj2);

        $this->assertIsArray($diff);
        $this->assertCount(3, $diff);

        $this->assertEquals('unchanged', $diff[0]['type']);
        $this->assertEquals('a', $diff[0]['key']);
        $this->assertEquals(1, $diff[0]['value']);

        $this->assertEquals('removed', $diff[1]['type']);
        $this->assertEquals('b', $diff[1]['key']);
        $this->assertEquals(2, $diff[1]['value']);

        $this->assertEquals('added', $diff[2]['type']);
        $this->assertEquals('c', $diff[2]['key']);
        $this->assertEquals(3, $diff[2]['value']);
    }

    public function testBuildDiffWithNestedObjects(): void
    {
        $obj1 = (object) [
            'a' => 1,
            'b' => (object) ['x' => 10, 'y' => 20]
        ];
        $obj2 = (object) [
            'a' => 1,
            'b' => (object) ['x' => 10, 'z' => 30]
        ];

        $diff = buildDiff($obj1, $obj2);

        $this->assertIsArray($diff);
        $this->assertCount(2, $diff);

        $this->assertEquals('unchanged', $diff[0]['type']);
        $this->assertEquals('a', $diff[0]['key']);

        $this->assertEquals('nested', $diff[1]['type']);
        $this->assertEquals('b', $diff[1]['key']);
        $this->assertArrayHasKey('children', $diff[1]);

        $children = $diff[1]['children'];
        $this->assertCount(3, $children);

        $this->assertEquals('unchanged', $children[0]['type']);
        $this->assertEquals('x', $children[0]['key']);

        $this->assertEquals('removed', $children[1]['type']);
        $this->assertEquals('y', $children[1]['key']);

        $this->assertEquals('added', $children[2]['type']);
        $this->assertEquals('z', $children[2]['key']);
    }

    public function testBuildDiffWithChangedValues(): void
    {
        $obj1 = (object) ['a' => 1, 'b' => 'old'];
        $obj2 = (object) ['a' => 1, 'b' => 'new'];

        $diff = buildDiff($obj1, $obj2);

        $this->assertIsArray($diff);
        $this->assertCount(2, $diff);

        $this->assertEquals('unchanged', $diff[0]['type']);
        $this->assertEquals('a', $diff[0]['key']);

        $this->assertEquals('changed', $diff[1]['type']);
        $this->assertEquals('b', $diff[1]['key']);
        $this->assertEquals('old', $diff[1]['oldValue']);
        $this->assertEquals('new', $diff[1]['newValue']);
    }

    public function testBuildDiffSortsKeys(): void
    {
        $obj1 = (object) ['z' => 1, 'a' => 2];
        $obj2 = (object) ['m' => 3, 'b' => 4];

        $diff = buildDiff($obj1, $obj2);

        $this->assertIsArray($diff);
        $this->assertCount(4, $diff);

        // Проверяем что ключи отсортированы по алфавиту
        $this->assertEquals('a', $diff[0]['key']);
        $this->assertEquals('b', $diff[1]['key']);
        $this->assertEquals('m', $diff[2]['key']);
        $this->assertEquals('z', $diff[3]['key']);
    }

    public function testGetFileFormat(): void
    {
        $this->assertEquals('json', getFileFormat('file.json'));
        $this->assertEquals('yaml', getFileFormat('file.yaml'));
        $this->assertEquals('yaml', getFileFormat('file.yml'));
    }

    public function testGetFileFormatWithUnsupportedFormat(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Unsupported file format: txt");

        getFileFormat('file.txt');
    }
}
