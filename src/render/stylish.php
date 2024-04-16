<?php

namespace Hexlet\P2\Render;

const INDENTCOUNT = 4;
const SPACE = ' ';

use function Hexlet\P2\Render\flatten;
use function Hexlet\P2\Render\render;

function stylish($diffObject)
{
    $diff = function($current, $depth) use (&$diff) {
        if (is_array($current)) {
            $currentKey = getKey($current);
            $currentStatus = $current['status'] ?? 'none';
            switch($current['status']) {
                case 'nested':
                    $children = array_map(
                        fn($child) => $diff($child, $depth + 1),
                        $current['children']
                    );
                    $prepared = flatten($children);
                    return prepareLine($depth, $currentKey, $prepared, 'same');
                    break;
                case 'old':
                case 'added':
                    $value = $current[$currentStatus];
                    break;
                case 'changed':
                    $resChanged = array_map(
                        fn($value, $status) => getLine($depth, $currentKey, $value, $status),
                        [$current['old'], $current['added']], ['old', 'added']);
                        return $resChanged;
                    break;
                case 'same':
                    $value = $current['old'];
                    break;
                default:
                    $value = array_values($current);
                    break;
                }
                return getLine($depth, $currentKey, $value, $currentStatus);
        }
        return $current;
            /*
            Построить пробелы (глубина на отступ)
            Построить отступ (заданы в обьекте - вывод, иначе выводить только пробелы?)
            Получить ключ (если задан - вывод )
            Вывод ключ - значение
                Значение массив ? 
                    да: рекурсивно обработать также детей, добавить {...дети пробел} 
                    нет: вывод значения как есть
                

            */
    };
    $array = array_map(fn($node) => $diff($node, 1), $diffObject);
    return render($array, false);
    

                        /*
                        Значения:
                        1. Строка (число)
                        2. Массив с 1 элементом
                        3. Массив с несколькими элементами

                        Заходит обьект
                        Парсится
                        Если статус nested:
                            Формирование отступа (пробелы и статус)
                            Формирование ключа
                            Формирование значения
                                Массив (с детьми)
                                    Рекурсивный заход
                            Вывод "{отступ}{статус}{ключ}{значение}"
                        Если статус Old, new
                            Формирование отступа (пробелы и статус)
                            Формирование ключа
                            Формирование значения
                                Массив
                                    Рекурсивный заход (такой же или отдельно?)
                                Не массив - вывод значения old/new
                            Вывод "{отступ}{статус}{ключ}{значение}"
                        Если статус Changed
                            Аналогично old
                            Аналогично new
                        Если статус same
                            Формирование отступа (пробелы и статус)
                            Формирование ключа
                            Формирование значения
                                Массив
                                    Рекурсивный заход (такой же или отдельно?)
                                Не массив - вывод значения old || new
                            Вывод "{отступ}{статус}{ключ}{значение}"
                        Слияние значений в одну строку
                            
                        Вопросы:
                            Обработка children и простого массива

                                */
                        // $children = array_reduce(
                            //     $current['children'],
                            //     fn($acc, $child) => array_merge($acc, $iter($child, $depth * 2)),
                            //     []
                            // );
                            // $prepared = implode("\n", ["{", ...$children, "{$currentIndent}}"]);
                            // $line = "{$currentIndent}{$currentKey}{$prepared}";
                            
           
}
function getLine($depth, $key, $value, $status = 'same') 
{
    $newValue = $value;
    if (is_array($value)) {
        $newValue = array_map(
            fn($nodeKey) => getLine($depth + 1, $nodeKey, $value[$nodeKey]),
            array_keys($value)); 
        }
        return prepareLine($depth, $key, $newValue, $status);
    }

function prepareLine($depth, $key, $value, $status)
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
    $value = toStringStylish($value);
    $separator = empty($value) ? '' : SPACE;
    return "{$currentSpace}{$currentStatus}{$key}:{$separator}{$value}";
}

function getKey($object) 
{
    if (array_key_exists('nodeKey', $object)) {
        return $object['nodeKey'];
    }
    return array_key_first($object);
}

function toStringStylish($value)
{
    switch(gettype($value)) {
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'NULL':
            return 'null';
        case 'string':
        case 'int':
        default:
            return $value;
    }
}
