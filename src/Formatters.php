<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\format as formatStylish;
use function Differ\Formatters\Plain\format as formatPlain;
use function Differ\Formatters\Json\format as formatJson;

function render(array $diff, string $formatName = 'stylish'): string
{
    switch ($formatName) {
        case 'stylish':
            return formatStylish($diff);
        case 'plain':
            return formatPlain($diff);
        case 'json':
            return formatJson($diff);
        default:
            throw new \Exception("Unknown format: '{$formatName}'");
    }
}
