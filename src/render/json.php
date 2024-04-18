<?php

namespace Hexlet\P2\Render;

use function Hexlet\P2\Render\flatten;
use function Hexlet\P2\Render\toString;

function json($diffObject)
{
    $diff = function ($current, $depth) use (&$diff) {
        if (is_array($current)) {
            $currentKey = getKeyJson($current);
            $currentStatus = $current['status'] ?? 'none';
            switch($current['status']) {
            case 'nested':
                $value = array_reduce(
                    $current['children'],
                    fn($acc, $child) => 
                        array_merge($acc, $diff($child, $depth + 1)),
                    []
                );
                return prepareLineJson($currentKey, ($value), 'same');
                break;
            case 'old':
            case 'added':
                $value = $current[$currentStatus];
                break;
            case 'changed':
                return flatten([
                    prepareLineJson($currentKey, $current['old'], 'old'),
                    prepareLineJson($currentKey, $current['added'], 'added')
                    ]);
                break;
            case 'same':
                $value = $current['old'];
                break;
            default:
                $value = array_values($current);
                break;
            }
                return prepareLineJson($currentKey, $value, $currentStatus);
        }
        return $current;
    };
    $array = array_reduce(
        $diffObject,
        fn($arr, $node) => array_merge($arr, $diff($node, 1)),
        []
    );
    return json_encode($array);
     
}

function prepareLineJson($key, $value, $status = 'none')
{
    $statusList = [
        'old' => '- ',
        'added' => '+ ',
        'same' => ' ',
        'none' => ''
    ];
    $currentStatus = $statusList[$status];
    $preparedkey = "{$currentStatus}{$key}";
    return [$preparedkey => toString($value)];
}

function getKeyJson($object) 
{
    if (array_key_exists('nodeKey', $object)) {
        return $object['nodeKey'];
    }
    return array_key_first($object);
}

