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
            // $prepare = function($value) => is(array($value) ? )
            $currentStatus = $current['status'] ?? 'none';
            switch($current['status']) {
                case 'nested':
                    $children = array_map(
                        fn($child) => $diff($child, $depth * 2),
                        $current['children']
                    );
                    // $value = flatten($children);
                    $prepared = implode(PHP_EOL, flatten($children));
                    // $value = flatten($children);
                    break;
                case 'old':
                case 'added':
                    $value = $current[$currentStatus]; //строка  или обычный массив
                    break;
                case 'changed':
                    $resChanged = array_map(
                        fn($value, $status) => getLine($depth, $currentKey, $value, $status),
                        [$current['old'], $current['added']], ['old', 'added']);
                        return $resChanged;
                        //массив вида [-oldkey:oldValue,  +newkey:newValue] - готовая строка
                    break;
                    //но не как у children
                case 'same':
                    $value = $current['old']; //строка или массив
                    break;
                default:
                    $value = array_values($current); //массив
                    break;
                }
                $value = is_array($value) ? array_map(fn($key) => "$key")
                $res =  getLine($depth, $currentKey, $value, $currentStatus);
                return $res;
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
    var_dump(render($array));
    die;
    
        //В конце возврат элемента общего массива в виде строки

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
function getLine($depth, $key, $value, $status = 'same' ) 
{
    $indentList = [
        'old' => '- ',
        'added' => '+ ',
        'same' => ' ',
        'none' => ''
    ];
    $indent = $indentList[$status];
    $spaces = $depth * INDENTCOUNT;
    $currentSpace = str_repeat(SPACE, $spaces - strlen($indent));
    if (is_array($value)) { //значение превратить в ключ + значение
        $options = array_map(
            fn($nodeKey) => getLine($depth *2, $nodeKey, $value[$nodeKey]),
            array_keys($value)); 
        $lines = implode(PHP_EOL, ['{', ...$options, "{$currentSpace}}"]);
        return "{$currentSpace}{$indent}{$key}: {$lines}";
        }
        $value = toStringStylish($value);
        return "{$currentSpace}{$indent}{$key}: {$value}";
    }

function prepareLine($depth, $key, $value, $status = 'same')
{
    if (is_array($value)) {
        $lines = implode(PHP_EOL, ['{', ...$value, "{$currentSpace}}"]);
        return "{$currentSpace}{$indent}{$key}: {$lines}";
    }
    $value = toStringStylish($value);
    return "{$currentSpace}{$indent}{$key}: {$value}";
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
            return trim(var_export($value, true), "'");
        default:
            return $value;
    }
}
/*

На выходе массив типа 0 => пробел знак ключ: значение
*/