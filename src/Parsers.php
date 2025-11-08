<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;
use Exception;

function parse(string $content, string $format): object
{
    return match ($format) {
        'json' => parseJson($content),
        'yaml', 'yml' => parseYaml($content),
        default => throw new \Exception("Unsupported format: {$format}")
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
    try {
        $data = Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);

        if (is_array($data)) {
            return (object) $data;
        }

        return $data;
    } catch (Exception $e) {
        throw new Exception("YAML parsing error: " . $e->getMessage());
    }
}

function getFileFormat(string $filePath): string
{
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);

    $supportedFormats = [
        'json' => 'json',
        'yaml' => 'yaml',
        'yml' => 'yaml'
    ];

    if (!array_key_exists($extension, $supportedFormats)) {
        throw new Exception("Unsupported file format: {$extension}");
    }

    return $supportedFormats[$extension];
}
