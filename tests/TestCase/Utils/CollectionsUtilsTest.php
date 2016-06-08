<?php
namespace Acciona\Users\Test\TestCase\Utils\Helper;

use Acciona\Users\Utils\CollectionsUtils;
use Cake\TestSuite\TestCase;

class CollectionsUtilsTest extends TestCase
{
    public function testFlapMapSimple()
    {
      $input = [[1, 2, 3, 4], [6, 7, 8, 9]];

      $result = CollectionsUtils::flatMap(function ($arr) {
        return array_map(function ($elem) { return $elem * 2; }, $arr);
      }, $input);

      $expected = [2, 4, 6, 8, 12, 14, 16, 18];

      $this->assertEquals($expected, $result);
    }

    public function testFlapMapFunction()
    {
      $input = [1, 2, 3, 4, 5];

      $result = CollectionsUtils::flatMap(function ($elem) {
        return [$elem * 2, $elem * 3];
      }, $input);

      $expected = [2, 3, 4, 6, 6, 9, 8, 12, 10, 15];

      $this->assertEquals($expected, $result);
    }
}
