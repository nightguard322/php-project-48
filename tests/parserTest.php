<?php

namespace Hexlet\P2\Tests;

use PHPUnit\Framework\TestCase;
use function Hexlet\P2\parse;

class ParserTest extends TestCase
{
  private $res;
  
  public function testParse()
  {
      $file = __DIR__ . '/fixtures/before.yml';
      $expected = [
        'host' => "hexlet.io",
        'timeout' => 50,
        'proxy' => "123.234.53.22",
        'follow' => false
    ];
      $this->assertEquals($expected, parse($file));
  }

}

// 