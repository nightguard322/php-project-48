<?php

namespace Hexlet\P2;

use Exception;
use Symfony\Component\Yaml\Yaml;

function yamlToArray($parsed): array
{
    $parsed = (array) $parsed;
    foreach ($parsed as $key => $value) {
        if (is_object($value)) {
            return yamlToArray($value);
        }
        $parsed[$key] = $value;
    }
    return $parsed;
}

function parse(string $path)
{
    if (is_file($path)) {
        $pathinfo = pathinfo($path);
        $file = file_get_contents($path);
    } else {
        throw new Exception($path);
    }
    switch ($pathinfo['extension']) {
        case 'json':
            return json_decode($file, true);
            break;
        case 'yml':
            $yaml = Yaml::parse($file, Yaml::PARSE_OBJECT_FOR_MAP);
            return yamlToArray($yaml);
            break;
        default:
            throw new Exception('Wrong extension');
    }
}
