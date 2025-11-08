<?php

// src/Formatters/json.php

namespace Differ\Formatters\Json;

function format(array $diff): string
{
    $json = json_encode($diff, JSON_PRETTY_PRINT);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception("JSON encoding error: " . json_last_error_msg());
    }

    return $json;
}
