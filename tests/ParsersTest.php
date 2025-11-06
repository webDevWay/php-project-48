<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Parsers\parse;
use function Differ\Parsers\parseFile;

class ParsersTest extends TestCase
{
    public function testParseJsonReturnsArray(): void
    {
        $json = '{"key": "value", "number": 42}';
        $result = parse($json, 'json');

        $this->assertEquals(['key' => 'value', 'number' => 42], $result);
    }

    public function testParseYamlReturnsArray(): void
    {
        $yaml = "key: value\nnumber: 42\nnested:\n  subkey: subvalue";
        $result = parse($yaml, 'yaml');

        $expected = [
            'key' => 'value',
            'number' => 42,
            'nested' => ['subkey' => 'subvalue']
        ];
        $this->assertEquals($expected, $result);
    }

    public function testParseYamlWithObjectsReturnsArray(): void
    {
        $yaml = "user:\n  name: John\n  age: 30";
        $result = parse($yaml, 'yaml');

        $this->assertIsArray($result);
        $this->assertIsArray($result['user']);
        $this->assertEquals('John', $result['user']['name']);
        $this->assertEquals(30, $result['user']['age']);
    }

    public function testParseComplexYamlStructure(): void
    {
        $yaml = <<<YAML
common:
  setting1: Value 1
  setting2: 200
  setting3: true
  setting6:
    key: value
    doge:
      wow: ''
group1:
  baz: bas
  foo: bar
  nest:
    key: value
group2:
  abc: 12345
  deep:
    id: 45
YAML;

        $result = parse($yaml, 'yaml');

        $this->assertIsArray($result);
        $this->assertIsArray($result['common']);
        $this->assertIsArray($result['common']['setting6']);
        $this->assertIsArray($result['common']['setting6']['doge']);
        $this->assertEquals('', $result['common']['setting6']['doge']['wow']);
    }

    public function testParseInvalidJsonThrowsException(): void
    {
        $invalidJson = '{"key": "value",}';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid JSON');

        parse($invalidJson, 'json');
    }

    public function testParseInvalidYamlThrowsException(): void
    {
        $invalidYaml = "key: value\n  indented: wrong";

        $this->expectException(\Exception::class);

        parse($invalidYaml, 'yaml');
    }

    public function testParseFileWithJsonExtension(): void
    {
        $result = parseFile('tests/fixtures/file1.json');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('common', $result);
    }

    public function testParseFileWithYamlExtension(): void
    {
        $result = parseFile('tests/fixtures/file1.yaml');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('common', $result);
    }

    public function testParseUnsupportedFormatThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported format: xml');

        parse('content', 'xml');
    }
}
