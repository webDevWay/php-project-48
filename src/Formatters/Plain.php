<?php

// src/Formatters/plain.php

namespace Differ\Formatters;

function formatPlain(array $diff): string
{
    $lines = buildLines($diff);
    return implode("\n", $lines);
}

function buildLines(array $diff, string $path = ''): array
{
    $lines = [];

    foreach ($diff as $node) {
        $currentPath = $path === '' ? $node['key'] : "{$path}.{$node['key']}";
        switch ($node['type']) {
            case 'nested':
                $nestedLines = buildLines($node['children'], $currentPath);
                $lines = array_merge($lines, $nestedLines);
                break;

            case 'added':
                $value = formatValue($node['value']);
                $lines[] = "Property '{$currentPath}' was added with value: {$value}";
                break;

            case 'removed':
                $lines[] = "Property '{$currentPath}' was removed";
                break;

            case 'changed':
                $oldValue = formatValue($node['oldValue']);
                $newValue = formatValue($node['newValue']);
                $lines[] = "Property '{$currentPath}' was updated. From {$oldValue} to {$newValue}";
                break;

            case 'unchanged':
                // Не выводим неизмененные свойства в plain формате
                break;
        }
    }

    return $lines;
}

function formatValue(mixed $value): string
{
    if (is_array($value)) {
        return '[complex value]';
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_string($value)) {
        return "'{$value}'";
    }

    return (string) $value;
}
