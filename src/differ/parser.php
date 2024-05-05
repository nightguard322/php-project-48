<?php

namespace Differ\Differ;

use Exception;
use Symfony\Component\Yaml\Yaml;

function yamlToArray(mixed $parsed): array
{
    return array_map(
        fn($file) =>
        is_object($file) ? yamlToArray($file) : $file,
        (array) $parsed
    );
}

function parse(string $path)
{
    if (is_file($path)) {
        $pathinfo = pathinfo($path);
        $file = file_get_contents($path);
        if ($file === false) { //!!!!!
            throw new Exception('file not found');
        }
    } else {
        throw new Exception($path);
    }
    $extension = $pathinfo['extension'] ?? null;
    switch ($extension) {
        case 'json':
            return json_decode($file, true);
        case 'yml':
        case 'yaml':
            $yaml = Yaml::parse($file, Yaml::PARSE_OBJECT_FOR_MAP);
            return yamlToArray($yaml);
        default:
            throw new Exception('Wrong Extension');
    }
}
