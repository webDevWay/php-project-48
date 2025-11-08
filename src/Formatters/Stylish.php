<?php

// src/Formatters/stylish.php

namespace Differ\Formatters;

function formatStylish(array $diff): string
{
    return buildOutput($diff, 1);
}

function buildOutput(array $diff, int $depth): string
{
    $indent = buildIndent($depth);
    $lines = array_map(function ($node) use ($depth, $indent) {
        return buildLine($node, $depth);
    }, $diff);

    $bracketIndent = buildIndent($depth - 1);
    return "{\n" . implode("\n", $lines) . "\n" . $bracketIndent . "}";
}

function buildLine(array $node, int $depth): string
{
    $key = $node['key'];
    $type = $node['type'];
    $indent = buildIndent($depth);

    switch ($type) {
        case 'nested':
            $children = buildOutput($node['children'], $depth + 1);
            return "{$indent}  {$key}: {$children}";

        case 'added':
            $value = toString($node['value'], $depth);
            return "{$indent}+ {$key}: {$value}";

        case 'removed':
            $value = toString($node['value'], $depth);
            return "{$indent}- {$key}: {$value}";

        case 'changed':
            $oldValue = toString($node['oldValue'], $depth);
            $newValue = toString($node['newValue'], $depth);
            return "{$indent}- {$key}: {$oldValue}\n{$indent}+ {$key}: {$newValue}";

        case 'unchanged':
            $value = toString($node['value'], $depth);
            return "{$indent}  {$key}: {$value}";

        default:
            throw new \Exception("Unknown node type: {$type}");
    }
}

function toString(mixed $value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        return buildOutput([], $depth + 1);
    }

    return (string) $value;
}

function buildIndent(int $depth): string
{
    return str_repeat('   ', $depth);
}
