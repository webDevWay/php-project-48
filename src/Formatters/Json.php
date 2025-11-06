<?php

// src/Formatters/json.php

namespace Differ\Formatters;

function formatJson(array $diff): string
{
    return json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
