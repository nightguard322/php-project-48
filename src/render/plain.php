<?php

namespace Hexlet\P2\Render;

use function Hexlet\P2\Render\toString;

function plain(array $diff): string
{
    $iter = function ($current, $path = '') use (&$iter) {
        return array_reduce(
            $current,
            function ($acc, $node) use ($path, $iter) {
                $currentKey = $path === '' ? $node['nodeKey'] : "{$path}.{$node['nodeKey']}";
                switch ($node['status']) {
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
                        $line = sprintf(
                            $string,
                            toString($currentKey),
                            toString($node['old']),
                            toString($node['added'])
                        );
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
