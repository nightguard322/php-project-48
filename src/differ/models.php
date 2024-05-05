<?php

namespace Differ\Differ;

function toString(mixed $value, bool $plain = true)
{
    switch (gettype($value)) {
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'NULL':
            return 'null';
        case 'array':
            return $plain ? '[complex value]' : $value;
        case 'string':
            return $plain ? "'{$value}'" : $value;
        case 'integer':
        default:
            return $value;
    }
}

function flatten(array $node, array $line = [])
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
