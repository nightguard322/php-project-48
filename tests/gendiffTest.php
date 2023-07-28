<?php

namespace Hexletp2\Tests;

use PHPUnit\Framework\TestCase;

class GendiffTest extends TestCase
{
  private $res;
  
  public function testOutputGendiff()
    {
      exec('./bin/gendiff bin/file1.json bin/file2.json', $this->res);
      $this->assertEquals(
'{
  - follow: false
    host: hexlet.io
  - proxy: 123.234.53.22
  - timeout: 50
  + timeout: 20
  + verbose: true
}'
        ,implode("\n", $this->res));
    }
}