<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\QueryBuilder\Ranges;

class RangesTest extends \PHPUnit_Framework_TestCase
{

    public function testBasicUsage(){
        $ranges =  Ranges::newRanges('startTime', 'period');

        $ranges->addCaseLessThan('early', 50);
        $ranges->addCaseBetween('normal', 50, 100);
        $ranges->addCaseGreaterThan('late',100);


        $ids = array_keys($ranges->params);
        $expected = "CASE WHEN `startTime` < :{$ids[0]} THEN 'early' " .
            "WHEN `startTime` BETWEEN :{$ids[1]} AND :{$ids[2]} THEN 'normal' " .
            "WHEN `startTime` > :{$ids[3]} THEN 'late' END AS period";

        $this->assertEquals($expected, $ranges->getCaseColumn());


    }

}