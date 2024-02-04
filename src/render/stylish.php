<?php

namespace Hexlet\P2\Render;

const INDENTCOUNT = 4;
const SPACE = ' ';

function stylish($diffObject)
{
    $iter = function ($current) use (&$iter) {
        if (is_array($current) && array_key_exists('status', $current)) {
            $values = getValues($current);
            $currentKey = $current['key'];
            if ($current['children']) {
                $preparedChildren = array_map(fn($child) => $iter($child), $current['children']);
                return [makeIndentWithKey('same', $currentKey) => $preparedChildren];
            }
            $indents = $current['status'] === 'changed' ? ['old', 'added'] : [$current['status']]; //nest => [key => value], nest => str, children => null, status => changed
            //values = [[$key => $value], str]
            //[[*spaces*key => value], str]
            $lines = [];
            for ($i = 0, $max = count($values); $i < $max; $i++) {
                $key = makeIndentWithKey($indents[$i], $currentKey);
                $lines[$key] = $iter($values[$i]);
            }
            return $lines;
            // $lines = array_map(
            //     fn($indent, $value) =>
            //         makeIndentWithKey($indent, $depth, $currentKey), $iter($value, $depth + 1), // ['- buz' => buzz, '+ buz' => bars,  foo => bar]
            //     $indents, 
            //     $values
            // );
            /*
            group1: {
                - baz: bas
                + baz: bars
                    foo: bar
                - nest: {
                        key: value
                    }
                + nest: str
                }

                [group1 => 
                    [
                    - baz => bas,
                    + baz => bars,
                    foo => bar,
                    - nest => [
                        key => value
                        ],
                    + nest => str
                    ];
                ]
            */
        } else {
            return $current;
            // $lines = array_map(
            //     fn($key, $value) =>
            //         makeIndentWithKey($status, $depth, $key) . ': ' . $iter($value, $depth + 1),
            //     array_keys($values), 
            //     $values
            // );
        };
    };
    return array_reduce(
        $diffObject, fn($acc, $leaf) => array_merge($acc, [$iter($leaf, 1)]),
        []
    );
    
}
function makeIndentWithKey($status, $key)
{
    $indentList = [
        'old' => '- ',
        'added' => '+ ',
        'same' => str_repeat(SPACE, 2)
    ];
    $indent = $indentList[$status];
    return "{$indent}{$key}";
}
// function makeIndentWithKey($status, $depth, $key)
// {
//     $indentList = [
//         'old' => '- ',
//         'added' => '+ ',
//         'same' => str_repeat(SPACE, 2)
//     ];
//     $indent = $indentList[$status];
//     $currentSpaces = str_repeat(SPACE, (($depth * INDENTCOUNT) - 2));
//     return "{$currentSpaces}{$indent}{$key}";
// }
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

