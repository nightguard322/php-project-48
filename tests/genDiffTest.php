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
      // $expected = file_get_contents(__DIR__ . '/fixtures/resultWithNested.txt');
      $expected = file_get_contents(__DIR__ . '/fixtures/resultStylish.txt');
      $before = __DIR__ . '/fixtures/beforeWithNested.yml';
      $after = __DIR__ . '/fixtures/afterWithNested.yml';
      $this->assertEquals($expected, genDiff($before, $after));
  }

}

// 