<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\format as formatStylish;
use function Differ\Formatters\Plain\format as formatPlain;
use function Differ\Formatters\Json\format as formatJson;

function format(array $diff, string $formatName = 'stylish'): string
{
    $formatters = [
        'stylish' => fn($diff) => formatStylish($diff),
        'plain' => fn($diff) => formatPlain($diff),
        'json' => fn($diff) => formatJson($diff)
    ];

    if (!array_key_exists($formatName, $formatters)) {
        throw new \Exception("Unknown format: {$formatName}");
    }

    return $formatters[$formatName]($diff);
}
