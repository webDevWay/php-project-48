<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $content, string $format): array
{
    return match ($format) {
        'json' => parseJson($content),
        'yaml', 'yml' => parseYaml($content),
        default => throw new \Exception("Unsupported format: {$format}")
    };
}

function parseFile(string $filepath): array
{
    $content = file_get_contents($filepath);
    if ($content === false) {
        throw new \Exception("Unable to read file: {$filepath}");
    }

    $extension = pathinfo($filepath, PATHINFO_EXTENSION);

    return parse($content, $extension);
}

function parseJson(string $content): array
{
    $data = json_decode($content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception("Invalid JSON: " . json_last_error_msg());
    }

    return $data;
}

function parseYaml(string $content): array
{
    try {
        $data = Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
        return objectToArray($data);
    } catch (\Exception $e) {
        throw new \Exception("Invalid YAML: " . $e->getMessage());
    }
}

function objectToArray(mixed $data)
{
    if (is_object($data)) {
        $data = (array) $data;
    }

    if (is_array($data)) {
        return array_map('Differ\Parsers\objectToArray', $data);
    }

    return $data;
}
