<?php
// src/Formatters/stylish.php

namespace Differ\Formatters;

function formatStylish(array $diff): string
{
    return iter($diff);
}

function iter(array $nodes, int $depth = 0): string
{
    $indent = str_repeat('    ', $depth);
    $lines = array_map(function ($node) use ($depth, $indent) {
        switch ($node['type']) {
            case 'nested':
                $children = iter($node['children'], $depth + 1);
                return "{$indent}    {$node['key']}: {$children}";
                
            case 'added':
                $value = toString($node['value'], $depth);
                return "{$indent}  + {$node['key']}: {$value}";
                
            case 'removed':
                $value = toString($node['value'], $depth);
                return "{$indent}  - {$node['key']}: {$value}";
                
            case 'changed':
                $oldValue = toString($node['oldValue'], $depth);
                $newValue = toString($node['newValue'], $depth);
                return "{$indent}  - {$node['key']}: {$oldValue}\n{$indent}  + {$node['key']}: {$newValue}";
                
            case 'unchanged':
                $value = toString($node['value'], $depth);
                return "{$indent}    {$node['key']}: {$value}";
                
            default:
                throw new \Exception("Unknown type: {$node['type']}");
        }
    }, $nodes);
    
    $bracketIndent = str_repeat('    ', max(0, $depth - 1));
    $result = ["{"];
    $result = array_merge($result, $lines);
    $result[] = "{$bracketIndent}}";
    
    return implode("\n", $result);
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
        return iter([], $depth + 1);
    }
    
    return (string) $value;
}