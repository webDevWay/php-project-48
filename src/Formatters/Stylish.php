<?php

namespace Differ\Formatters\Stylish;

function formatStylish(array $diff): string
{
    return iter($diff, 0);
}

function iter(array $nodes, int $depth): string
{
    $indent = str_repeat('    ', $depth);
    $lines = array_map(function ($node) use ($depth) {
        $type = $node['type'];
        $key = $node['key'];
        $currentIndent = str_repeat('    ', $depth + 1);

        switch ($type) {
            case 'nested':
                $children = iter($node['children'], $depth + 1);
                return "{$currentIndent}  {$key}: {$children}";

            case 'added':
                $value = stringify($node['value'], $depth + 1);
                return "{$currentIndent}+ {$key}: {$value}";

            case 'removed':
                $value = stringify($node['value'], $depth + 1);
                return "{$currentIndent}- {$key}: {$value}";

            case 'changed':
                $oldValue = stringify($node['oldValue'], $depth + 1);
                $newValue = stringify($node['newValue'], $depth + 1);
                return "{$currentIndent}- {$key}: {$oldValue}\n{$currentIndent}+ {$key}: {$newValue}";

            case 'unchanged':
                $value = stringify($node['value'], $depth + 1);
                return "{$currentIndent}  {$key}: {$value}";

            default:
                throw new \Exception("Unknown type: {$type}");
        }
    }, $nodes);

    return "{\n" . implode("\n", $lines) . "\n{$indent}}";
}

function stringify(mixed $value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        return iter([], $depth);
    }

    return (string) $value;
}
