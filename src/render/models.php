<?php

namespace Hexlet\P2\Render;

function toString($value)
{
    switch (gettype($value)) {
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

function flatten($node, $line = [])
{
    return array_reduce(
        array_keys($node),
        fn($acc, $key) =>
            is_array($node[$key])
            ?
            flatten($node[$key], $acc)
            :
            array_merge($acc, [$key => $node[$key]]),
        $line
    );
}

function render(array $diff, bool $plain = true)
{
    if ($plain) {
        $line = flatten($diff, []);
    } else {
        $line = ["{", ...$diff, "}"];
    }
    return implode("\n", $line);
}
