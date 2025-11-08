<?php

// src/Formatters/json.php

namespace Differ\Formatters\Json;

function format(array $diff): string
{
    $json = json_encode($diff, JSON_PRETTY_PRINT  | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception("JSON encoding error: " . json_last_error_msg());
    }

    return $json;
}
