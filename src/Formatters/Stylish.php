<?php

namespace Hexlet\Code\Formatters;

function formatStylish($diff, $depth = 1)
{
    $indent = str_repeat('    ', $depth - 1);
    $lines = array_map(function ($item) use ($depth, $indent) {
        switch ($item['type']) {
            case 'added':
                $value = formatValue($item['value'], $depth);
                return "{$indent}  + {$item['key']}: {$value}";
            case 'removed':
                $value = formatValue($item['value'], $depth);
                return "{$indent}  - {$item['key']}: {$value}";
            case 'unchanged':
                $value = formatValue($item['value'], $depth);
                return "{$indent}    {$item['key']}: {$value}";
            case 'changed':
                $value1 = formatValue($item['value1'], $depth);
                $value2 = formatValue($item['value2'], $depth);
                return "{$indent}  - {$item['key']}: {$value1}\n{$indent}  + {$item['key']}: {$value2}";
            case 'nested':
                $children = formatStylish($item['children'], $depth + 1);
                return "{$indent}    {$item['key']}: {$children}";
            default:
                return '';
        }
    }, $diff);
    
    $bracketIndent = str_repeat('    ', $depth - 1);
    return "{\n" . implode("\n", $lines) . "\n{$bracketIndent}}";
}

function formatValue($value, $depth)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    if (is_object($value)) {
        $indent = str_repeat('    ', $depth);
        $bracketIndent = str_repeat('    ', $depth - 1);
        
        $properties = array_map(function ($key) use ($value, $depth) {
            $formattedValue = formatValue($value->$key, $depth + 1);
            $currentIndent = str_repeat('    ', $depth);
            return "{$currentIndent}{$key}: {$formattedValue}";
        }, array_keys(get_object_vars($value)));
        
        return "{\n" . implode("\n", $properties) . "\n{$bracketIndent}}";
    }
    return $value;
}