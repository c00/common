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
use c00\QueryBuilder\Comparison;
use c00\QueryBuilder\QryHelper;

class ComparisonTest extends \PHPUnit_Framework_TestCase
{

    public function testComparisons1(){

        $c = new Comparison("user", '=', "Peter");

        $c->uniqueId = 123456;

        $expected = "`user` = :123456";
        $this->assertEquals($expected, $c->toString());
    }

    public function testComparisons2(){

        $c = new Comparison("table.user", '=', "Peter");

        $c->uniqueId = 123456;

        $expected = "`table`.`user` = :123456";
        $this->assertEquals($expected, $c->toString());
    }


    public function testComparisons3(){
        $c = new Comparison("table.user", '=', "**Peter");

        $c->uniqueId = 123456;

        $expected = "`table`.`user` = `Peter`";
        $this->assertEquals($expected, $c->toString());
    }

    public function testComparisons4(){
        $c = new Comparison("table.user", '=', "**table.Peter");

        $c->uniqueId = 123456;

        $expected = "`table`.`user` = `table`.`Peter`";
        $this->assertEquals($expected, $c->toString());
    }

    public function testNullComparisons(){
        $c = new Comparison("table.user", '=', null);

        $c->uniqueId = 123456;

        $expected = "`table`.`user` IS NULL";
        $this->assertEquals($expected, $c->toString());
    }

    public function testNullComparisons2(){
        $c = new Comparison("table.user", 'IS', null);

        $c->uniqueId = 123456;

        $expected = "`table`.`user` IS NULL";
        $this->assertEquals($expected, $c->toString());
    }

}