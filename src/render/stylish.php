<?php

namespace Hexlet\P2\Render;

const INDENTCOUNT = 4;
const SPACE = ' ';

function stylish($diffObject)
{
    //где то просачиваются элементы обьекта вместо values
    $iter = function ($current, $depth) use (&$iter, $diffObject) {
        if (!is_array($current)) {
            return $current;
        }
        echo "объект - \n";
        var_dump($current);
        $key = $current['key'];
        echo "ключ: $key\n";
        if ($current['children']) {
            $preparedChildren = array_map(fn($child) => $iter($child, $depth + 1), $current['children']);
            $lines = ["{", ...$preparedChildren, "}"];
            return makeIndentWithKey('same', $depth, $key) . ': ' . implode("\n", $lines);
        }
        $values = getValues($current);
        $status = array_key_exists('status', $current) ? $current['status'] : $current['same'];
        $indents = $status === 'changed' ? ['old', 'added'] : [$status];
        $lines = array_map(
            fn($indent, $value) =>
                makeIndentWithKey($indent, $depth, $key) . ': ' . $iter($value, $depth + 1),
            $indents, $values);
        return implode("\n", $lines);
    };
    $diffBody = array_reduce(
        $diffObject, fn($acc, $leaf) => array_merge($acc, [$iter($leaf, 1)]),
        []
    );
    $result = ["{", ...$diffBody, "}"];
    return implode("\n", $result);
    
}
function makeIndentWithKey($status, $depth, $key)
{
    $indentList = [
        'old' => '- ',
        'added' => '+ ',
        'same' => str_repeat(SPACE, 2)
    ];
    $indent = $indentList[$status];
    $currentSpaces = str_repeat(SPACE, (($depth * INDENTCOUNT) - 2));
    return "{$currentSpaces}{$indent}{$key}";
}
function getValues($diffObject)
{
    switch ($diffObject['status']) {
        case 'old': case 'same':
            return [$diffObject['old']];
        case 'added':
            return [$diffObject['added']];
        case 'changed':
            return [$diffObject['old'], $diffObject['added']];
        default:
            return $diffObject;
        }
}

