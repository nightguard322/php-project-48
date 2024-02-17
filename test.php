<?php

use function Functional\retry;

function stringify(mixed $object, string $indent = '', int $count = 1): string
{
    if (!is_array($object)) {
        return toString($object);
    }
    $iter = function($current, $depth) use (&$iter, $indent, $count) {
        $currentIndentWidth = $count * $depth;
        $currentIndent = str_repeat($indent, $currentIndentWidth);
        $closedBracket = str_repeat($indent, $currentIndentWidth - $count);
        if (is_array($current)) {
            $lines = array_map(fn($key, $value) => "{$currentIndent}{$key}: {$iter($value, $depth + 1)}", array_keys($current), $current);
            return implode("\n", ['{', ...$lines, "{$closedBracket}}"]);
        }
        return $current;
    };
    $prepared = $iter($object, 1);
    return $prepared;
}

function toString(mixed $object): string
{
    return var_export($object, true);
}






// var_dump(stringify('hello')); // hello – значение приведено к строке, но не имеет кавычек
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

var_dump(stringify($data, '|-', 2));
// Символ, переданный вторым аргументом повторяется столько раз, сколько указано третьим аргументом.
// {
// |-|-hello: world
// |-|-is: true
// |-|-nested: {
// |-|-|-|-count: 5
// |-|-}
// }