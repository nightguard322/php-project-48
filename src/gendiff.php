<?php

namespace Differ\Differ;

use Exception;

use function Differ\Differ\parse;
use function Differ\Differ\Stylish;
use function Differ\Differ\plain;
use function Differ\Differ\json;

function genDiff(string $file1, string $file2, string $format = 'stylish')
{
    $firstFile = parse($file1);
    $secondFile = parse($file2);
    $ast = makeAst($firstFile, $secondFile);
    return getFormat($format, $ast);
}

function makeAst($file1, $file2)
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
                        $data = buildNode('nested', $key, null, null, makeAst($file1[$key], $file2[$key]));
                    } elseif ($file1[$key] === $file2[$key]) {
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
        'nodeKey' => $key,
        'old' => $oldValue,
        'added' => $newValue,
        'children' => $children
    ];
    return $node;
}

function getFormat($format, $diffObject)
{
    switch ($format) {
        case 'stylish':
            return stylish($diffObject);
            break;
        case 'plain':
            return plain($diffObject);
            break;
        case 'json':
            return json($diffObject);
            break;
        default:
            throw new Exception('Wrong format');
    }
}
