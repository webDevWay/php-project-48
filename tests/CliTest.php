<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{
    public function testHelpOutput(): void
    {
        $output = shell_exec('./bin/gendiff -h');
        $this->assertStringContainsString('Generate diff', $output);
        $this->assertStringContainsString('Usage:', $output);
        $this->assertStringContainsString('Options:', $output);
        $this->assertStringContainsString('--help', $output);
        $this->assertStringContainsString('--version', $output);
        $this->assertStringContainsString('--format <fmt>', $output);
        $this->assertStringContainsString('<firstFile> <secondFile>', $output);
    }

    public function testVersionOutput(): void
    {
        $output = shell_exec('./bin/gendiff -v');
        $this->assertEquals("gendiff 1.0.0\n", $output);
    }

    /*public function testBasicUsage(): void
    {
        $file1 = __DIR__ . '/fixtures/file1.json';
        $file2 = __DIR__ . '/fixtures/file2.json';

        $output = shell_exec("./bin/gendiff {$file1} {$file2}");
        $this->assertStringContainsString('{', $output);
        $this->assertStringContainsString('}', $output);
    }*/

    public function testWithFormatOption(): void
    {
        $file1 = __DIR__ . '/fixtures/file1.json';
        $file2 = __DIR__ . '/fixtures/file2.json';

        $output = shell_exec("./bin/gendiff --format plain {$file1} {$file2}");
        $this->assertIsString($output);
        $this->assertNotEmpty($output);
    }

    public function testDefaultFormat(): void
    {
        $file1 = __DIR__ . '/fixtures/file1.json';
        $file2 = __DIR__ . '/fixtures/file2.json';

        $output = shell_exec("./bin/gendiff {$file1} {$file2}");
        // Проверяем что используется stylish формат по умолчанию
        $this->assertStringContainsString('{', $output);
        $this->assertStringContainsString('}', $output);
    }
}
