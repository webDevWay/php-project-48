<?php
// src/Formatters/stylish.php

namespace Differ\Formatters;

function formatStylish(array $diff): string
{
    return buildTree($diff, 1);
}

function buildTree(array $diff, int $depth): string
{
    $indent = buildIndent($depth);
    $bracketIndent = buildIndent($depth - 1);
    
    $lines = array_map(function ($node) use ($depth, $indent) {
        return buildLine($node, $depth, $indent);
    }, $diff);
    
    $result = ["{"];
    $result = array_merge($result, $lines);
    $result[] = "{$bracketIndent}}";
    
    return implode("\n", $result);
}

function buildLine(array $node, int $depth, string $indent): string
{
    $key = $node['key'];
    $type = $node['type'];
    
    switch ($type) {
        case 'nested':
            $children = buildTree($node['children'], $depth + 1);
            return "{$indent}  {$key}: {$children}";
            
        case 'added':
            $value = stringify($node['value'], $depth);
            return "{$indent}+ {$key}: {$value}";
            
        case 'removed':
            $value = stringify($node['value'], $depth);
            return "{$indent}- {$key}: {$value}";
            
        case 'changed':
            $oldValue = stringify($node['oldValue'], $depth);
            $newValue = stringify($node['newValue'], $depth);
            return "{$indent}- {$key}: {$oldValue}\n{$indent}+ {$key}: {$newValue}";
            
        case 'unchanged':
            $value = stringify($node['value'], $depth);
            return "{$indent}  {$key}: {$value}";
            
        default:
            throw new \Exception("Unknown node type: {$type}");
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
    
    $indent = buildIndent($depth);
    $bracketIndent = buildIndent($depth - 1);
    
    $lines = array_map(function ($key, $val) use ($depth, $indent) {
        $formattedValue = stringify($val, $depth + 1);
        return "{$indent}  {$key}: {$formattedValue}";
    }, array_keys($value), $value);
    
    $result = ["{"];
    $result = array_merge($result, $lines);
    $result[] = "{$bracketIndent}  }";
    
    return implode("\n", $result);
}

function buildIndent(int $depth): string
{
    return str_repeat('    ', $depth);
}