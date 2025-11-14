<?php

namespace Differ\Formatters\Json;

function format(array $diff): string
{
    $json = json_encode($diff, JSON_PRETTY_PRINT  | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    return $json;
}
