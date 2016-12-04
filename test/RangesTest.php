<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;


use c00\common\CovleDate;
use c00\QueryBuilder\Qry;
use c00\QueryBuilder\Ranges;

class RangesTest extends \PHPUnit_Framework_TestCase
{

    public function testBasicUsage(){
        $ranges =  Ranges::newRanges('startTime', 'period');

        $ranges->addCaseLessThan('early', 50);
        $ranges->addCaseBetween('normal', 50, 100);
        $ranges->addCaseGreaterThan('late',100);

        $ranges->getCaseColumn();
        $expected = "CASE WHEN `startTime` < 50 THEN 'early' " .
            "WHEN `startTime` BETWEEN 50 AND 100 THEN 'normal' " .
            "WHEN `startTime` > 100 THEN 'late' END AS period";

        $this->assertEquals($expected, $ranges->getCaseColumn());


    }

}