<?php

namespace Hexlet\P2\Render;

function stylish($diffObject)
{
    $iter = function ($current, $depth) use (&$iter, $diffObject) {
        $values = getValues($diffObject);
        $indents = $diffObject['status'] === 'changed' ? ['oldValue' , 'newValue'] : [$diffObject['status']];
        $spaces = str_repeat(' ', 4) . $depth;
        // $result = array_map(fn($indent, $value) => is_array($value) ? makeData($spaces, $indent, $diffObject['key'], )
        //     if (is_array($value)) {
        //         return "{$spaces}{$indent}{$diffObject['key']}: {$iter($value, $depth + 1) } "; 
        //     } else {
        //         return ""
        //     }
        // }, $indents, $values);
    };
    $diffBody = array_reduce(
        $diffObject, fn($acc, $leaf) => array_merge($acc, $iter($leaf)),
        []
    );

}

function getValues($diffObject)
{
    switch (true) {
        case ($diffObject['oldValue'] && $diffObject['newValue']) :
            return [prepare($diffObject['oldValue']), prepare($diffObject['newValue'])];
            break;
        case $diffObject['oldValue'] :
            return [prepare($diffObject['oldValue'])];
            break;
        default :
            return [prepare($diffObject['newValue'])];
        }
}

function prepare($value)
{
    return trim(var_export($value, true, "'"));
}