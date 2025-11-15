<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use Exception;

function parse(string $content, string $extension): object
{
    return match ($extension) {
        'json' => parseJson($content),
        'yaml', 'yml' => parseYaml($content),
        default => throw new \Exception("Unsupported format: {$extension}")
    };
}

function parseJson(string $content): object
{
    $data = json_decode($content);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON parsing error: " . json_last_error_msg());
    }

    return $data;
}

function parseYaml(string $content): object
{
    $data = Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);

    return $data;
}
