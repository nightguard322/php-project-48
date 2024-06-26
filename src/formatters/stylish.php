<?php

namespace Differ\Differ;

const INDENTCOUNT = 4;
const SPACE = ' ';

use function Differ\Differ\flatten;
use function Differ\Differ\toString;

function stylish(array $diffObject)
{
    $diff = function ($current, $depth) use (&$diff) {
        if (is_array($current)) {
            $currentKey = $current['nodeKey'] ?? array_key_first($current);
            $currentStatus = $current['status'] ?? 'none';
            switch ($current['status']) {
                case 'nested':
                    $children = array_map(
                        fn($child) => $diff($child, $depth + 1),
                        $current['children']
                    );
                    return prepareLine($depth, $currentKey, flatten($children), 'same');
                case 'old':
                case 'added':
                    $value = $current[$currentStatus];
                    break;
                case 'changed':
                    return implode(
                        PHP_EOL,
                        [
                        getLine($depth, $currentKey, $current['old'], 'old'),
                        getLine($depth, $currentKey, $current['added'], 'added')
                        ]
                    );
                case 'same':
                    $value = $current['old'];
                    break;
                default:
                    $value = array_values($current);
            }
                return getLine($depth, $currentKey, $value, $currentStatus);
        }
        return $current;
    };
    $array = array_map(fn($node) => $diff($node, 1), $diffObject);
    return render($array, false);
}

function getLine(int $depth, string $key, mixed $value, string $status = 'same')
{
    if (is_array($value)) {
        $newValue = array_map(
            fn($nodeKey) => getLine($depth + 1, $nodeKey, $value[$nodeKey]),
            array_keys($value)
        );
        return prepareLine($depth, $key, $newValue, $status);
    }
    return prepareLine($depth, $key, $value, $status);
}

function prepareLine(int $depth, string $key, mixed $value, string $status)
{
    $statusList = [
        'old' => '- ',
        'added' => '+ ',
        'same' => ' ',
        'none' => ''
    ];
    $currentStatus = $statusList[$status];
    $indentSpace = $depth * INDENTCOUNT;
    $currentSpace = str_repeat(SPACE, $indentSpace - strlen($currentStatus));
    $bracketSpace = str_repeat(SPACE, $indentSpace);
    if (is_array($value)) {
        $lines = implode(PHP_EOL, ['{', ...$value, "{$bracketSpace}}"]);
        return "{$currentSpace}{$currentStatus}{$key}: {$lines}";
    }
    $preparedValue = toString($value, false);
    return "{$currentSpace}{$currentStatus}{$key}: {$preparedValue}";
}
