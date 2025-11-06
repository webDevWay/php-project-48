<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\DiffBuilder\buildDiff;

class DiffBuilderTest extends TestCase
{
    public function testBuildDiffDoesNotMutateInputArrays(): void
    {
        $data1 = ['c' => 1, 'a' => 2, 'b' => 3];
        $data2 = ['b' => 4, 'd' => 5];

        $originalData1 = $data1;
        $originalData2 = $data2;

        buildDiff($data1, $data2);

        $this->assertEquals($originalData1, $data1);
        $this->assertEquals($originalData2, $data2);
    }

    public function testBuildDiffReturnsKeysInSortedOrder(): void
    {
        $data1 = ['z' => 1, 'a' => 2, 'm' => 3];
        $data2 = ['b' => 4, 'd' => 5];

        $diff = buildDiff($data1, $data2);
        $keys = array_map(fn($node) => $node['key'], $diff);

        $this->assertEquals(['a', 'b', 'd', 'm', 'z'], $keys);
    }

    public function testBuildDiffHandlesNestedArrays(): void
    {
        $data1 = ['a' => ['b' => 1]];
        $data2 = ['a' => ['b' => 2, 'c' => 3]];

        $diff = buildDiff($data1, $data2);

        $this->assertEquals('a', $diff[0]['key']);
        $this->assertEquals('nested', $diff[0]['type']);
        $this->assertIsArray($diff[0]['children']);
    }

    public function testBuildDiffHandlesAllNodeTypes(): void
    {
        $data1 = ['removed' => 1, 'changed' => 2, 'unchanged' => 3];
        $data2 = ['added' => 4, 'changed' => 5, 'unchanged' => 3];

        $diff = buildDiff($data1, $data2);

        $types = array_map(fn($node) => $node['type'], $diff);
        $this->assertContains('added', $types);
        $this->assertContains('removed', $types);
        $this->assertContains('changed', $types);
        $this->assertContains('unchanged', $types);
    }
}
