<?php

// src/Differ.php

namespace Differ;

use function Differ\DiffBuilder\buildDiff;
use function Differ\Parsers\parseFile;
use function Differ\Formatters\format;

function genDiff(string $filepath1, string $filepath2, string $format = 'stylish'): string
{
    $data1 = parseFile($filepath1);
    $data2 = parseFile($filepath2);

    $diff = buildDiff($data1, $data2);

    return format($diff, $format);
}
