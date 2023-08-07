<?php

namespace Hexlet\P2\Tests;

use PHPUnit\Framework\TestCase;
use function Hexlet\P2\genDiff;
use function Hexlet\P2\parse;

class GendiffTest extends TestCase
{
  private $res;
  
  public function testGendiff()
  {
      $expected = file_get_contents(__DIR__ . '/fixtures/resultWithNested.txt');
      $before = __DIR__ . '/fixtures/beforeWithNested.json';
      $after = __DIR__ . '/fixtures/afterWithNested.json';
      $this->assertEquals($expected, genDiff($before, $after));
  }

}

// 