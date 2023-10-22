<?php

namespace Hexlet\P2\Render;

const INDENTCOUNT = 4;
const SPACE = ' ';

function stylish($diffObject)
{
    $iter = function ($current, $depth) use (&$iter, $diffObject) {
        //var_dump($diffObject);
        $key = $current['key'];
        // $spaces = str_repeat(SPACES, $depth);
        if ($current['children']) {
            $preparedChildren = array_map(fn($child) => $iter($child, $depth + 1), $current['children']);
            $lines = ["{", ...$preparedChildren, "}"];
            return makeIndentWithKey('same', $depth, $key) . ': ' . implode("\n", $lines);
        }
        $values = getValues($current);
        $indents = $current['status'] === 'changed' ? ['old' , 'added'] : [$current['status']];
        $lines = array_map(    
            fn($indent, $value) => is_array($value) 
            ?
            makeIndentWithKey($indent, $depth, $key) . ': ' . $iter($value, $depth + 1)
            :
            makeIndentWithKey($indent, $depth, $key) . ': ' . $value,
        $indents,
        $values);

        return implode("\n", $lines);
    };
    $diffBody = array_reduce(
        $diffObject, fn($acc, $leaf) => array_merge($acc, [$iter($leaf, 1)]),
        []
    );
    $result = ["{", ...$diffBody, "}"];
    return implode("\n", $result);
    
}
function makeIndentWithKey($indent, $depth, $key)
{
    $indentList = [
        'old' => '- ',
        'added' => '+ ',
        'same' => str_repeat(SPACE, 2)
    ];
    $currentSpaces = str_repeat(SPACE, (($depth * INDENTCOUNT) - 2));
    // echo "Ключ - {$key} Глубина - {$depth} Результат - {$currentSpaces} Количество - " . ($depth * INDENTCOUNT - 2) . "\n";
    return "{$currentSpaces}{$indentList[$indent]}{$key}";
}
function getValues($diffObject)
{
    switch (true) {
        case (!is_null($diffObject['oldValue']) && !is_null($diffObject['newValue'])) :
            // echo "старое - " . $diffObject['oldValue'] . "и новое - " .  $diffObject['newValue']  . "\n";
            return [toString($diffObject['oldValue']), toString($diffObject['newValue'])];
            break;
        case !is_null($diffObject['oldValue']) :
            // echo 'старое' . "\n";
            return [toString($diffObject['oldValue'])];
            break;
        case !is_null($diffObject['newValue']) :
            // echo 'новое'. "\n";
            return [toString($diffObject['newValue'])];
        }
}

function toString($value)
{
    return trim(var_export($value, true), "'");
}