<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatters\render;
use function Functional\sort;

function genDiff(string $filePath1, string $filePath2, string $format = 'stylish'): string
{
    $content1 = getFileContent($filePath1);
    $content2 = getFileContent($filePath2);

    $extension1 = pathinfo($filePath1, PATHINFO_EXTENSION);
    $extension2 = pathinfo($filePath2, PATHINFO_EXTENSION);

    $data1 = parse($content1, $extension1);
    $data2 = parse($content2, $extension2);

    $diff = buildDiff($data1, $data2);
    return render($diff, $format);
}

function getFileContent(string $filePath): string
{
    if (!file_exists($filePath)) {
        throw new \Exception("File '{$filePath}' does not exist");
    }

    $content = file_get_contents($filePath);
    if ($content === false) {
        throw new \Exception("Failed to read file '{$filePath}'");
    }

    return $content;
}

function buildDiff(object $data1, object $data2): array
{
    $keys1 = array_keys(get_object_vars($data1));
    $keys2 = array_keys(get_object_vars($data2));
    $allKeys = array_unique(array_merge($keys1, $keys2));
    $sortedKeys = sortKeys($allKeys);

    return array_map(function ($key) use ($data1, $data2) {
        return buildNode($key, $data1, $data2);
    }, $sortedKeys);
}

function buildNode(string $key, object $data1, object $data2): array
{
    $value1 = getValue($data1, $key);
    $value2 = getValue($data2, $key);

    $hasInFirst = property_exists($data1, $key);
    $hasInSecond = property_exists($data2, $key);

    if (!$hasInFirst) {
        return buildAddedNode($key, $value2);
    }

    if (!$hasInSecond) {
        return buildRemovedNode($key, $value1);
    }

    if (isObject($value1) && isObject($value2)) {
        return buildNestedNode($key, $value1, $value2);
    }

    if ($value1 === $value2) {
        return buildUnchangedNode($key, $value1);
    }

    return buildChangedNode($key, $value1, $value2);
}

function buildAddedNode(string $key, mixed $value): array
{
    return [
        'type' => 'added',
        'key' => $key,
        'value' => $value
    ];
}

function buildRemovedNode(string $key, mixed $value): array
{
    return [
        'type' => 'removed',
        'key' => $key,
        'value' => $value
    ];
}

function buildUnchangedNode(string $key, mixed $value): array
{
    return [
        'type' => 'unchanged',
        'key' => $key,
        'value' => $value
    ];
}

function buildChangedNode(string $key, mixed $oldValue, mixed $newValue): array
{
    return [
        'type' => 'changed',
        'key' => $key,
        'oldValue' => $oldValue,
        'newValue' => $newValue
    ];
}

function buildNestedNode(string $key, object $oldValue, object $newValue): array
{
    return [
        'type' => 'nested',
        'key' => $key,
        'children' => buildDiff($oldValue, $newValue)
    ];
}

function getValue(object $data, string $key): mixed
{
    return property_exists($data, $key) ? $data->$key : null;
}

function isObject(mixed $value): bool
{
    return is_object($value);
}

function sortKeys(array $keys): array
{
    return sort($keys, function ($a, $b) {
        return strcmp($a, $b);
    });
}
