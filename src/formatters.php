<?php

namespace Differ\Differ;
use Exception;

use function Differ\Differ\stylish;
use function Differ\Differ\plain;
use function Differ\Differ\json;

function getFormat($format, $diffObject)
{
    switch ($format) {
        case 'stylish':
            return stylish($diffObject);
            break;
        case 'plain':
            return plain($diffObject);
            break;
        case 'json':
            return json($diffObject);
            break;
        default:
            throw new Exception('Wrong format');
    }
}