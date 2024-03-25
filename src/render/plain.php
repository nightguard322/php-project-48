<?php

namespace Hexlet\P2\Render;

function plain (array $diff): string
{
    $iter = function($current, $path = '') use (&$iter){
        return array_reduce(
            $current,
            function ($acc, $node) use ($path, $iter) {
                $currentKey = $path === '' ? $node['nodeKey'] : "{$path}.{$node['nodeKey']}";
                switch($node['status']) {
                    case 'nested':
                        $line = $iter($node['children'], $currentKey); //[]
                        break;
                    case 'old':
                        $string = "Property %s was removed";
                        $line = sprintf($string, toString($currentKey));
                        break;
                    case 'added':
                        $string = 'Property %s was added with value: %s';
                        $line = sprintf($string, toString($currentKey), toString($node['added']));
                        break;
                    case 'changed':
                        $string = "Property %s was updated. From %s to %s";
                        $line = sprintf($string, toString($currentKey), toString($node['old']), toString($node['added']));
                        break;
                    default:
                    return $acc;
                }
                return array_merge($acc, [$line]);
            },
            []
        );
    };
    return render($iter($diff));
}

/*
1. Пройти по дереву (ast) с reduce
2. Если есть изменения
    2.1 Выводить {ключ} {изменение} ({старое значение}) {новое значение}
3. Если ребенок 
    3.1 Добавить ключ родителя
    3.2 Снова пункт 1 для детей
*/
function toString($value)
{
    switch(gettype($value)) {
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'array':
            return '[complex value]';
        case 'NULL':
            return 'null';
        case 'string' || 'int':
            return var_export($value, true);
        default:
            return $value;
    }
}