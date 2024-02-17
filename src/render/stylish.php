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
                $newChildren = array_reduce($preparedChildren, fn($acc, $child) => array_merge($acc, $child), []);
                $result = [makeIndentWithKey('same', $currentKey) => $newChildren];
                return $result;
            }
            $indents = $current['status'] === 'changed' ? ['old', 'added'] : [$current['status']];
            $lines = [];
            for ($i = 0, $max = count($values); $i < $max; $i++) {
                $key = makeIndentWithKey($indents[$i], $currentKey);
                $lines[$key] = $iter($values[$i]);
            }
            return $lines;
        } else {
            return $current;
        };
    };
    $result = array_reduce(
        $diffObject, fn($acc, $leaf) => array_merge($acc, $iter($leaf, 1)),
        []
    );
    return render($result);  
}

function render(mixed $diffObject)
{
    $iter = function ($current, $depth) use (&$iter) {
        if (is_array($current)) {
            $currentIndentWidth = INDENTCOUNT * $depth;
            $currentSpace = str_repeat(SPACE, $currentIndentWidth);
            $closingBracket = str_repeat(SPACE, $currentIndentWidth - INDENTCOUNT);
            $lines = array_map(fn($key, $child) => "{$currentSpace}{$key}: {$iter($child, $depth + 1)}", array_keys($current), $current);
            $preparedLines = ['{', ...$lines, "{$closingBracket}}"];
            return implode("\n", $preparedLines);
        }
            return $current;
    };
    $result = $iter($diffObject, 1);
    return $result;
}

function makeIndentWithKey($status, $key)
{
    $indentList = [
        'old' => '- ',
        'added' => '+ ',
        'same' => '  '
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

