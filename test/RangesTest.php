<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\components\Ranges;

class RangesTest extends \PHPUnit_Framework_TestCase
{

    public function testBasicUsage(){
        $ranges =  Ranges::newRanges('startTime', 'period');

        $ranges->addCaseLessThan('early', 50);
        $ranges->addCaseBetween('normal', 50, 100);
        $ranges->addCaseGreaterThan('late',100);


        $ps = new ParamStore();
        $actual = $ranges->toString($ps);
        $ids = array_keys($ps->getParams());

        $expected = "CASE WHEN `startTime` < :{$ids[0]} THEN 'early' " .
            "WHEN `startTime` BETWEEN :{$ids[1]} AND :{$ids[2]} THEN 'normal' " .
            "WHEN `startTime` > :{$ids[3]} THEN 'late' END";

        $this->assertEquals($expected, $actual);
    }


    public function testDeprecated(){
        $ranges =  \c00\QueryBuilder\Ranges::newRanges('startTime', 'period');

        $ranges->addCaseLessThan('early', 50);
        $ranges->addCaseBetween('normal', 50, 100);
        $ranges->addCaseGreaterThan('late',100);


        $ps = new ParamStore();
        $actual = $ranges->toString($ps);
        $ids = array_keys($ps->getParams());

        $expected = "CASE WHEN `startTime` < :{$ids[0]} THEN 'early' " .
            "WHEN `startTime` BETWEEN :{$ids[1]} AND :{$ids[2]} THEN 'normal' " .
            "WHEN `startTime` > :{$ids[3]} THEN 'late' END";

        $this->assertEquals($expected, $actual);
    }

}