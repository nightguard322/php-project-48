<?php

namespace Hexlet\Code\Showdoc;

use Docopt;

function showDoc()
{
$doc = <<<'DOCOPT'
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)

Options:
  -h --help                     Show this screen
  -v --version                  Show version
DOCOPT;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
      require_once(__DIR__ . '/../vendor/autoload.php');
  } else {
      require_once(__DIR__ . '/../../vendor/autoload.php');
  }
$args = Docopt::handle($doc, array('version'=>'Naval Fate 2.0'));
foreach ($args as $k=>$v)
    echo $k.': '.json_encode($v).PHP_EOL;
}