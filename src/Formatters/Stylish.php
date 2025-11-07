<?php

// src/Formatters/stylish.php

namespace Differ\Formatters;

function formatStylish(array $diff): string
{
    return iter($diff, 1);
}

function iter(array $diff, int $depth): string
{
    $indent = buildIndent($depth);
    $bracketIndent = buildIndent($depth - 1);

    $lines = array_map(function ($node) use ($depth, $indent) {
        $type = $node['type'];
        $key = $node['key'];

        switch ($type) {
            case 'added':
                $value = stringify($node['value'], $depth);
                return "{$indent}+ {$key}: {$value}";
            case 'removed':
                $value = stringify($node['value'], $depth);
                return "{$indent}- {$key}: {$value}";
            case 'unchanged':
                $value = stringify($node['value'], $depth);
                return "{$indent}  {$key}: {$value}";
            case 'changed':
                $oldValue = stringify($node['oldValue'], $depth);
                $newValue = stringify($node['newValue'], $depth);
                return "{$indent}- {$key}: {$oldValue}\n{$indent}+ {$key}: {$newValue}";
            case 'nested':
                $children = iter($node['children'], $depth + 1);
                return "{$indent}  {$key}: {$children}";
            default:
                throw new \Exception("Unknown type: {$type}");
        }
    }, $diff);

    return implode("\n", [
        "{",
        ...$lines,
        "{$bracketIndent}}"
    ]);
}

function stringify(mixed $value, int $depth): string
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

    $indent = buildIndent($depth);
    $bracketIndent = buildIndent($depth - 1);

    $lines = array_map(function ($key, $val) use ($depth, $indent) {
        $formattedValue = stringify($val, $depth + 1);
        return "{$indent}  {$key}: {$formattedValue}";
    }, array_keys($value), $value);

    return implode("\n", [
        "{",
        ...$lines,
        "{$bracketIndent}  }"
    ]);
}

function buildIndent(int $depth): string
{
    return str_repeat('      ', $depth);
}
