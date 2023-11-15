<?php

namespace Hexlet\P2;

use function Hexlet\P2\parse;
use function Hexlet\P2\Render\stylish;

function genDiff(string $file1, string $file2)
{
    $firstFile = parse($file1);
    $secondFile = parse($file2);
    $diffObject = showDiff(toString($firstFile), toString($secondFile));
    return stylish($diffObject);
}

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
                    if (is_array($file1[$key]) && (is_array($file2[$key]))) {
                        $data = buildNode('nested', $key, null, null, showDiff($file1[$key], $file2[$key]));
                    }
                    elseif ($file1[$key] === $file2[$key]) {
                        $data = buildNode('same', $key, $file1[$key]);
                    } else {
                        $data = buildNode('changed', $key, $file1[$key], $file2[$key]); //два значения
                    }
                    break;
                case array_key_exists($key, $file1):
                    $data = buildNode('old', $key, $file1[$key]); //только старое
                    break;
                case array_key_exists($key, $file2):
                    $data = buildNode('added', $key, null, $file2[$key]); //только новое
                    break;
            }
            $acc[] = $data;
            return $acc;
        },
        []
    );
    return $difference;
    // return "{\n" . implode("\n", $difference) . "\n}";
}

function buildNode(string $status, $key, $oldValue, $newValue = null, $children = null)
{
    $node = [
        'status' => $status,
        'key' => $key,
        'old' => $oldValue,
        'added' => $newValue,
        'children' => $children
    ];
    return $node;
}

function toString($value)
{
    if (is_array($value)) 
        return array_map(fn($current) => toString($current), $value);
    return is_null($value) ? 'null' : trim(var_export($value, true), "'");
}
//With windows env
