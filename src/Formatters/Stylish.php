<?php

// src/Formatters/stylish.php

namespace Differ\Formatters;

function formatStylish(array $diff): string
{
    return buildOutput($diff);
}

function buildOutput(array $diff, int $depth = 0): string
{
    $lines = array_map(function ($node) use ($depth) {
        return formatNode($node, $depth);
    }, $diff);

    return "{\n" . implode("\n", $lines) . "\n" . indent($depth) . "}";
}

function formatNode(array $node, int $depth): string
{
    $key = $node['key'];

    switch ($node['type']) {
        case 'nested':
            $children = buildOutput($node['children'], $depth + 1);
            return indent($depth) . "  {$key}: {$children}";

        case 'added':
            $value = stringify($node['value'], $depth + 1);
            return indent($depth) . "+ {$key}: {$value}";

        case 'removed':
            $value = stringify($node['value'], $depth + 1);
            return indent($depth) . "- {$key}: {$value}";

        case 'changed':
            $oldValue = stringify($node['oldValue'], $depth + 1);
            $newValue = stringify($node['newValue'], $depth + 1);
            return indent($depth) . "- {$key}: {$oldValue}\n" .
                   indent($depth) . "+ {$key}: {$newValue}";

        case 'unchanged':
            $value = stringify($node['value'], $depth + 1);
            return indent($depth) . "  {$key}: {$value}";

        default:
            throw new \Exception("Unknown node type: {$node['type']}");
    }
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

    $lines = [];
    foreach ($value as $key => $val) {
        $formattedValue = stringify($val, $depth + 1);
        $lines[] = indent($depth) . "    {$key}: {$formattedValue}";
    }

    return "{\n" . implode("\n", $lines) . "\n" . indent($depth - 1) . "  }";
}

function indent(int $depth): string
{
    return str_repeat('    ', $depth);
}
