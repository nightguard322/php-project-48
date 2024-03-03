<?php

namespace Hexlet\P2\Render;

const INDENTCOUNT = 4;
const SPACE = ' ';

function stylish($diffObject)
{
    $iter = function ($current) use (&$iter) {
        if (is_array($current)) {
            $currentKeys = getKey($current);
            if ($current['children']) {
                $children = array_map(
                    fn($child) => 
                    $iter($child),
                    $current['children']
                );
                $preparedChilren = array_reduce($children, fn($acc, $child) => array_merge($acc, $child), []);
                return array_map(fn($currentKey) => ['none', $currentKey, $preparedChilren], $currentKeys);
            }
            $currentValues = getValues($current);
            $currentStatus = array_key_exists('status', $current) ?
            $current['status'] 
            :
            'nested';
            $indents = $currentStatus === 'changed' ? ['old', 'added'] : [$currentStatus];
            
            $lines = array_map(
                fn($indent, $currentKey, $child) => 
                    [$indent, $currentKey, $iter($child)],
                $indents,
                $currentKeys,
                $currentValues
            );
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
            $currentSpaceLenght = INDENTCOUNT * $depth;
            $lines = array_map(
                function ($child) use ($iter, $currentSpaceLenght, $depth) {
                    [$status, $key, $value] = $child;
                    $currentIndent = makeIndentWithKey($status, $currentSpaceLenght);
                    $currentValue = $iter($value, $depth + 1);
                    $currentValueIndent = $currentValue ? SPACE : '';
                    $res = "{$currentIndent}{$key}:{$currentValueIndent}{$currentValue}";
                    return $res;
                }, 
                $current
            );
            $closingBracketIndent = str_repeat(SPACE, $currentSpaceLenght - INDENTCOUNT);
            $preparedLines = ['{', ...$lines, "{$closingBracketIndent}}"];
            $result = implode("\n", $preparedLines);
            return $result;
        }
            return $current;
    };
    $res = $iter($diffObject, 1);
    return $res;
}

function makeIndentWithKey($status, $currentSpaceLenght)
{
    $indentList = [
        'old' => '- ',
        'added' => '+ ',
        'same' => ' ',
        'nested' => ''
    ];
    $indent = $indentList[$status];
    $statusLenght = strlen($indent);
    $currentSpace = str_repeat(SPACE, $currentSpaceLenght - $statusLenght);
    return "{$currentSpace}{$indent}";
}

function getKey($object) 
{
    if (array_key_exists('nodeKey', $object)) {
        return $object['status'] === 'changed' ?
        [$object['nodeKey'] , $object['nodeKey']] 
        :
        [$object['nodeKey']];
    }
    return array_keys($object);
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
        return array_values($diffObject);
    }
}

function toString($value)
{
    return trim(var_export($value, true), "'");
}