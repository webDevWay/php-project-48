<?php

namespace Differ\Formatters\Stylish;

function format(array $diff): string
{
    $lines = buildLines($diff);
    return "{\n" . implode("\n", $lines) . "\n}";
}

function buildLines(array $diff, int $depth = 1): array
{
    $lines = array_map(function ($node) use ($depth) {
        return buildLine($node, $depth);
    }, $diff);

    return $lines;
}

function buildLine(array $node, int $depth): string
{
    $indent = buildIndent($depth);
    $key = $node['key'];

    switch ($node['type']) {
        case 'added':
            $value = formatValue($node['value'], $depth);
            return "{$indent}+ {$key}: {$value}";

        case 'removed':
            $value = formatValue($node['value'], $depth);
            return "{$indent}- {$key}: {$value}";

        case 'unchanged':
            $value = formatValue($node['value'], $depth);
            return "{$indent}  {$key}: {$value}";

        case 'changed':
            $oldValue = formatValue($node['oldValue'], $depth);
            $newValue = formatValue($node['newValue'], $depth);
            return "{$indent}- {$key}: {$oldValue}\n{$indent}+ {$key}: {$newValue}";

        case 'nested':
            $children = buildLines($node['children'], $depth + 1);
            $formattedChildren = implode("\n", $children);
            $closingIndent = buildIndent($depth);
            return "{$indent}  {$key}: {\n{$formattedChildren}\n{$closingIndent}  }";

        default:
            throw new \Exception("Unknown node type: {$node['type']}");
    }
}

function formatValue(mixed $value, int $depth): string
{
    if (is_object($value)) {
        $properties = get_object_vars($value);
        $lines = array_map(function ($key) use ($value, $depth) {
            $formattedValue = formatValue($value->$key, $depth + 1);
            $indent = buildIndent($depth + 1);
            return "{$indent}  {$key}: {$formattedValue}";
        }, array_keys($properties));

        $closingIndent = buildIndent($depth);
        return "{\n" . implode("\n", $lines) . "\n{$closingIndent}  }";
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    return (string) $value;
}

function buildIndent(int $depth): string
{
    $depth = $depth * 2 - 2;
    return str_repeat('  ', $depth);
}
