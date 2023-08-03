<?php

namespace Hexlet\P2\Tests;

use PHPUnit\Framework\TestCase;
use function Hexlet\P2\genDiff;
/**
 * @covers ::genDiff
 */

class GendiffTest extends TestCase
{
  private $res;
  
  public function testGendiff()
  {
      $expected = file_get_contents(__DIR__ . '/fixtures/result.txt');
      $before = __DIR__ . '/fixtures/before.json';
      $after = __DIR__ . '/fixtures/after.json';
      $this->assertEquals($expected, genDiff($before, $after));
  }
}

// 