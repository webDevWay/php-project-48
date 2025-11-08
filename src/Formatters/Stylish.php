<?php

namespace Differ\Formatters\Stylish;

use function Funct\Collection\flattenAll;

function formatStylish(array $diff, int $depth = 1): string
{
    $indent = str_repeat('    ', $depth - 1);
    $lines = array_map(function ($node) use ($depth, $indent) {
        switch ($node['type']) {
            case 'nested':
                $formattedChildren = formatStylish($node['children'], $depth + 1);
                return "{$indent}    {$node['key']}: {$formattedChildren}";
            case 'added':
                $formattedValue = formatValue($node['value'], $depth + 1);
                return "{$indent}  + {$node['key']}: {$formattedValue}";
            case 'removed':
                $formattedValue = formatValue($node['value'], $depth + 1);
                return "{$indent}  - {$node['key']}: {$formattedValue}";
            case 'changed':
                $oldValue = formatValue($node['oldValue'], $depth + 1);
                $newValue = formatValue($node['newValue'], $depth + 1);
                return "{$indent}  - {$node['key']}: {$oldValue}\n{$indent}  + {$node['key']}: {$newValue}";
            case 'unchanged':
                $formattedValue = formatValue($node['value'], $depth + 1);
                return "{$indent}    {$node['key']}: {$formattedValue}";
            default:
                throw new \Exception("Unknown node type: {$node['type']}");
        }
    }, $diff);

    return "{\n" . implode("\n", $lines) . "\n{$indent}}";
}

function formatValue($value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (!is_array($value)) {
        return (string) $value;
    }

    if (empty($value)) {
        return '{}';
    }

    $indent = str_repeat('    ', $depth);
    $lines = array_map(function ($key) use ($value, $depth, $indent) {
        $formattedValue = formatValue($value[$key], $depth + 1);
        return "{$indent}    {$key}: {$formattedValue}";
    }, array_keys($value));

    return "{\n" . implode("\n", $lines) . "\n{$indent}}";
}
