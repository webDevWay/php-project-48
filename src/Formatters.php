<?php

// src/Formatters.php

namespace Differ\Formatters;

use function Differ\Formatters\formatStylish;
use function Differ\Formatters\formatPlain;
use function Differ\Formatters\formatJson;

function format(array $diff, string $formatName): string
{
    return match ($formatName) {
        'stylish' => formatStylish($diff),
        'plain' => formatPlain($diff),
        'json' => formatJson($diff),
        default => throw new \Exception("Unknown format: {$formatName}")
    };
}
