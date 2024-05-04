<?php

namespace Differ\Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\parse;

class ParserTest extends TestCase
{
    private $res;
    public function testParse()
    {
        $file = __DIR__ . '/fixtures/beforeWithNested.yml';
        $expected = [
            "common" => [
              "setting1" => "Value 1",
              "setting2" => 200,
              "setting3" => true,
              "setting6" => [
                "key" => "value",
                "doge" => [
                  "wow" => ""
                ]
              ]
            ],
            "group1" => [
              "baz" => "bas",
              "foo" => "bar",
              "nest" => [
                "key" => "value"
              ]
            ],
            "group2" => [
              "abc" => 12345,
              "deep" => [
                "id" => 45
              ]
            ]
            ];
        $this->assertEquals($expected, parse($file));
    }
}
