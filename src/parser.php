<?php

namespace Hexlet\Code\Parser;

use Exception;

function parse(string $path)
{
    if (is_file($path)) {
        $file = file_get_contents($path);
    } else {
        throw new Exception('Wrong path');
    }
    return json_decode($file, true);
}
