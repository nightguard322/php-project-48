<?php

namespace Hexlet\P2\Render;

function toString($value)
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

function flatten($node, $line = [])
{
    return array_reduce(
        $node,
        fn($acc, $leaf) => 
            is_array($leaf) ? flatten($leaf, $acc) : array_merge($acc, [$leaf]),
        $line
    );
};
function render(array $diff, bool $plain = true)
{
    if ($plain) {
        $line = flatten($diff, []);
    } else {
        $line = ["{", ...$diff, "}"];
    }
    die;
    return implode("\n", $line);
}