<?php

namespace Hexlet\P2\Render;

const INDENTCOUNT = 4;
const SPACE = ' ';

function stylish($diffObject)
{
    $iter = function ($current, $depth) use (&$iter) {
        if (is_array($current)) {
            $values = getValues($current);
            if (array_key_exists('status', $current)) {
                $currentKey = $current['key'];
                if ($current['children']) {
                    $preparedChildren = array_map(fn($child) => $iter($child, $depth + 1), $current['children']);
                    $lines = ["{", ...$preparedChildren, "}"];
                    return makeIndentWithKey('same', $depth, $currentKey) . ': ' . implode("\n", $lines);
                }
                $indents = $current['status'] === 'changed' ? ['old', 'added'] : [$current['status']];
                $lines = array_map(
                    fn($indent, $value) =>
                        makeIndentWithKey($indent, $depth, $currentKey) . ': проверка' . $iter($value, $depth + 1),
                    $indents, $values);
            } else {
                $status = 'same';
                var_dump($current);
                $lines = array_map(
                    fn($key, $value) =>
                        makeIndentWithKey($status, $depth, $key) . ': ' . $iter($value, $depth + 1),
                    array_keys($values), $values);
            };
            return implode("\n", $lines);
        } else {
            return $current;
        }
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

