<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;


use c00\common\CovleDate;
use c00\common\Helper;

class HelperTest extends \PHPUnit_Framework_TestCase
{

    public function testUniqueIds(){
        $iterations = 10000;

        $array = [];

        for ($i = 0; $i < $iterations; $i++){
            $id = Helper::getUniqueId($array);
            $array[$id] = "meh";
        }

        $this->assertEquals($iterations, count($array));

    }

}