<?php

namespace Differ\Differ;

use Exception;

use function Differ\Differ\stylish;
use function Differ\Differ\plain;
use function Differ\Differ\json;

function getFormat(string $format, array $diffObject)
{
    switch ($format) {
        case 'stylish':
            return stylish($diffObject);
        case 'plain':
            return plain($diffObject);
        case 'json':
            return json($diffObject);
        default:
            throw new Exception('Wrong format');
    }
}
