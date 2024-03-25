<?php

namespace Hexlet\P2\Render;

// function toLine($node, $line) 
// {
//     // return array_reduce(
//     //     $node,
//     //     function($acc, $node){
//     //         return is_array($node) ? toLine($node, $acc) : array_merge($acc, [$node]);
//     //     },
//     //     []
//     // );
//     return array_reduce(
//         $node,
//         fn($acc, $leaf) => 
//             is_array($leaf) ? toLine($leaf, $acc) : array_merge($acc, [$leaf]),
//         $line
//     );
// }

function render(array $diff)
{
    $toLine = function($node, $line) use (&$toLine) {
        return array_reduce(
            $node,
            fn($acc, $leaf) => 
                is_array($leaf) ? $toLine($leaf, $acc) : array_merge($acc, [$leaf]),
            $line
        );
    };
    $line = $toLine($diff, []);
    return implode("\n", $line);
}