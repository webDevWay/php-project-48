<?php

namespace Differ\Differ;

use function Differ\Parsers\{getFileFormat, parse};
use function Differ\DiffBuilder\buildDiff;
use function Differ\Formatters\format;

function genDiff(string $filePath1, string $filePath2, string $format = 'stylish'): string
{
    $data1 = getDataFromFile($filePath1);
    $data2 = getDataFromFile($filePath2);

    $diff = buildDiff($data1, $data2);
    return format($diff, $format);
}

function getDataFromFile(string $filePath): object
{
    if (!file_exists($filePath)) {
        throw new \Exception("File '{$filePath}' does not exist");
    }

    $content = file_get_contents($filePath);
    if ($content === false) {
        throw new \Exception("Failed to read file '{$filePath}'");
    }

    $fileFormat = getFileFormat($filePath);
    return parse($content, $fileFormat);
}
