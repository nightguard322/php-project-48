<?php

namespace Hexlet\P2;

use Exception;

function parse(string $path): array
{
    if (is_file($path)) {
        $file = file_get_contents($path);
    } else {
        throw new Exception('Wrong path');
    }
    return json_decode($file, true);
}
