<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Parsers\{parse, getFileFormat};

class ParsersTest extends TestCase
{
    public function testParseJsonReturnsObject(): void
    {
        $json = '{"key": "value", "number": 123}';
        $result = parse($json, 'json');

        $this->assertIsObject($result);
        $this->assertEquals('value', $result->key);
        $this->assertEquals(123, $result->number);
    }

    public function testParseYamlReturnsObject(): void
    {
        $yaml = "key: value\nnumber: 123";
        $result = parse($yaml, 'yaml');

        $this->assertIsObject($result);
        $this->assertEquals('value', $result->key);
        $this->assertEquals(123, $result->number);
    }

    public function testParseYamlWithNestedStructure(): void
    {
        $yaml = "parent:\n  child: value";
        $result = parse($yaml, 'yaml');

        $this->assertIsObject($result);
        $this->assertIsObject($result->parent);
        $this->assertEquals('value', $result->parent->child);
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

    public function testParseWithInvalidJson(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("JSON parsing error");

        parse('invalid json', 'json');
    }

    public function testParseWithInvalidYaml(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("YAML parsing error");

        parse("invalid: yaml: content", 'yaml');
    }

    public function testParseWithUnsupportedFormat(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Unsupported format: xml");

        parse('<xml></xml>', 'xml');
    }
}
