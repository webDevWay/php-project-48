<?php

// src/DiffBuilder.php

namespace Differ\DiffBuilder;

use function Funct\Collection\sortBy;

function buildDiff(array $data1, array $data2): array
{
    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    $sortedKeys = sortBy($keys, fn($key) => $key);

    $diff = [];

    foreach ($sortedKeys as $key) {
        $value1 = $data1[$key] ?? null;
        $value2 = $data2[$key] ?? null;

        if (!array_key_exists($key, $data1)) {
            $diff[] = [
                'key' => $key,
                'type' => 'added',
                'value' => $value2
            ];
        } elseif (!array_key_exists($key, $data2)) {
            $diff[] = [
                'key' => $key,
                'type' => 'removed',
                'value' => $value1
            ];
        } elseif (is_array($value1) && is_array($value2)) {
            $diff[] = [
                'key' => $key,
                'type' => 'nested',
                'children' => buildDiff($value1, $value2)
            ];
        } elseif ($value1 === $value2) {
            $diff[] = [
                'key' => $key,
                'type' => 'unchanged',
                'value' => $value1
            ];
        } else {
            $diff[] = [
                'key' => $key,
                'type' => 'changed',
                'oldValue' => $value1,
                'newValue' => $value2
            ];
        }
    }

    return $diff;
}
