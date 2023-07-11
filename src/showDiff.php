<?php

namespace Hexlet\Code\Showdiff;

function showDiff($file1, $file2)
{
    $keys = array_merge(array_keys($file1), array_keys($file2));
    $map = array_unique($keys);
    sort($map);
    $difference = array_reduce(
        $map,
        function ($acc, $key) use ($file1, $file2) {
            switch (true) {
                case (array_key_exists($key, $file1) && array_key_exists($key, $file2)):
                    $data = $file1[$key] === $file2[$key]
                    ?
                    buildNode('same', $key, $file1[$key])
                    :
                    buildNode('changed', $key, $file1[$key], $file2[$key]);
                    break;
                case array_key_exists($key, $file1):
                    $data = buildNode('old', $key, $file1[$key]);
                    break;
                case array_key_exists($key, $file2):
                    $data = buildNode('added', $key, $file2[$key]);
                    break;
            }
            return array_merge($acc, $data);
        },
        []
    );
    return "{\n". implode("\n", $difference) . "\n}";
}

function buildNode(string $status, $key, $old, $new = null)
{
    $current = getValue($old);
    $newValue = $new ? getValue($new) : null;
    $indentList = [
        'same' => ' ',
        'added' => '+',
        'old' => '-'
    ];
    return $status === 'changed'
    ?
    ["{$indentList['old']} {$key}: $current", "{$indentList['added']} {$key}: $newValue"]
    :
    ["{$indentList[$status]} {$key}: $current"];
}

function getPath($path)
{
    $path = str_replace('\\', '/', $path);
    if ($path[1] === ':') {
        $replace = substr($path, 0, 2);
        $path = str_replace($replace, '/mnt/' . strtolower($path[0]), $path);
    }
    return is_file($path) ? $path : getcwd() . $path;
}

function getValue($value)
{
    switch (gettype($value)) {
        case 'boolean':
            return $value ? 'true' : 'false';
        default:
            return $value;
    }
}
