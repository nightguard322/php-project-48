<?php

namespace Differ\Differ;

use Exception;

use function Differ\Differ\parse;
use function Differ\Differ\getFormat;
use function Differ\Differ\arraySort;

function genDiff(string $file1, string $file2, string $format = 'stylish')
{
    $firstFile = parse($file1);
    $secondFile = parse($file2);
    $ast = makeAst($firstFile, $secondFile);
    return getFormat($format, $ast);
}

function makeAst(array $file1, array $file2)
{
    $keys = array_merge(array_keys($file1), array_keys($file2));
    $map = array_unique($keys);
    $difference = array_reduce(
        arraySort($map),
        function ($ast, $key) use ($file1, $file2) {
            $data = [];
            switch (true) {
                case (array_key_exists($key, $file1) && array_key_exists($key, $file2)):
                    if (is_array($file1[$key]) && (is_array($file2[$key]))) {
                        $data[] = buildNode('nested', $key, null, null, makeAst($file1[$key], $file2[$key]));
                    } elseif ($file1[$key] === $file2[$key]) {
                        $data[] = buildNode('same', $key, $file1[$key]);
                    } else {
                        $data[] = buildNode('changed', $key, $file1[$key], $file2[$key]); //два значения
                    }
                    break;
                case array_key_exists($key, $file1):
                    $data[] = buildNode('old', $key, $file1[$key]); //только старое
                    break;
                case array_key_exists($key, $file2):
                    $data[] = buildNode('added', $key, null, $file2[$key]); //только новое
                    break;
            };
                $newAst = array_merge($ast, [$data]);
                return $newAst;
        },
        []
    );
    return $difference;
    // return "{\n" . implode("\n", $difference) . "\n}";
}

function buildNode(string $status, string $key, mixed $oldValue, mixed $newValue = null, array $children = null)
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
