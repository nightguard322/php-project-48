<?php

function stringify(mixed $object): string
{
    if (!is_array($object)) {
        return toString($object);
    }
    $iter = function($current, $depth) use (&$iter) {
        
    };
    $result = array_map(fn($element) => $iter($element, 1), $object);
    return implode("\n", ["{", ...$result, "}"]);
}

function toString(mixed $object): string
{
    return var_export($object, true);
}







var_dump(stringify('hello')); // hello – значение приведено к строке, но не имеет кавычек
stringify(true);    // true
stringify(5);       // 5

$data = [
    'hello' => 'world',
    'is' => true,
    'nested' => ['count' => 5],
];

stringify($data); // то же самое что stringify(data, ' ', 1);
// {
//  hello: world
//  is: true
//  nested: {
//   count: 5
//  }
// }