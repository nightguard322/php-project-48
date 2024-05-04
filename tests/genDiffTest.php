<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GendiffTest extends TestCase
{
  private $res;
  
  public function testGendiff()
  {
      $expected = file_get_contents(__DIR__ . '/fixtures/resultStylish.txt');
      $before = __DIR__ . '/fixtures/beforeWithNested.yml';
      $after = __DIR__ . '/fixtures/afterWithNested.yml';
      $this->assertEquals($expected, genDiff($before, $after));
  }
}
