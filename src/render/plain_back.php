<?php

namespace Hexlet\P2\Render\Plain;

const INDENTCOUNT = 4;
const SPACE = ' ';

function plain($diffObject)
{
    print_r($diffObject);die;
    $iter = function ($current, $path = '') use (&$iter) {
        if (is_array($current) && array_key_exists('nodeKey', $current)) {
            $currentKey = $path === '' ? "{$current['nodeKey']}" : "{$path}.{$current['nodeKey']}"; //"{$path}.{$current['nodeKey']}";
            if ($current['children']) { //follow
                $children = array_map(
                    fn($child) => 
                    $iter($child, $currentKey),
                    $current['children'] 
                );
                return array_reduce($children, fn($acc, $child) => array_merge($acc, $child), []);
                // return array_map(fn($currentKey) => ['none', $currentKey, $preparedChilren], $currentKey);
            }
            $currentValues = getValues($current);
            $currentStatus = $current['status'];
            // $indents = $currentStatus === 'changed' ? ['old', 'added'] : [$currentStatus];
            $lines = array_map(
                fn($value) => 
                    [$currentStatus, $currentKey, $iter($value, $currentKey)],
                $currentValues
            );
            return $lines;
        } else {
            return $current;
        };
    };
    $result = array_reduce(
        $diffObject, fn($acc, $leaf) => array_merge($acc, $iter($leaf)),
        [] 
    );
    print_r($result);
    die;
    return renderPlain($result);  
}

function renderPlain(mixed $diffObject)
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

function getKey($object, $path = '') 
{
    if (array_key_exists('nodeKey', $object)) {
        return ["{$path}{$object['nodeKey']}"];
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

/*
было: 
1. Не изменилось - не интересно
2. added - добавилось, Вывод $space=' ' . $status = '+'. $key = 'follow': $value ='false'
3. old - Вывод $space=' ' . $status = '-'. $key = 'follow': $value ='false'
4. changed - изменилось, Вывод $space=' ' . $status = '-'. $key = 'follow': $value ='old' + $space=' ' . $status = '+'. $key = 'follow': $value ='added'
5. children = рекурсия с добавлением родителя
стало:

1. Не изменилось - не интересно
2. added - добавилось, Property $key = 'common.follow' was $status = 'added' with value: $value ='false'
3. old - Property $key = 'common.setting2' was $status = removed
4. changed - добавилось, Property $key = 'common.setting6.doge.wow' was $status = 'updated'. From if(arr){[complex value]} $old = '' to $new = 'so much'
5. children = заходим в детей с добавлением уровня родителя в ключ и идем до вывода детей 

*/