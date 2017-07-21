<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;


use c00\QueryBuilder\components\Comparison;
use c00\QueryBuilder\components\Where;
use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\QueryBuilderException;

class WhereTest extends \PHPUnit_Framework_TestCase
{

    public function testWhere(){
        $ps = new ParamStore();
        $w = Where::new('name', '=', 'peter');
        $w->isFirst = true;

        $actual = $w->toString($ps);
        $params = array_keys($ps->getParams());
        $expected = " WHERE `name` = :{$params[0]}";
        $this->assertEquals($expected, $actual);

        $w->isFirst = false;
        $actual = $w->toString($ps);
        $params = array_keys($ps->getParams());
        $expected = " AND `name` = :{$params[0]}";
        $this->assertEquals($expected, $actual);

        $w->type = Comparison::TYPE_OR;
        $actual = $w->toString($ps);
        $params = array_keys($ps->getParams());
        $expected = " OR `name` = :{$params[0]}";
        $this->assertEquals($expected, $actual);

        //Will not cause an exception, because th eID already exists.
        $w->toString();
    }

    public function testWhereException(){
        $w = Where::new('name', '=', 'peter');
        $w->isFirst = true;

        $this->expectException(QueryBuilderException::class);
        $w->toString();
    }

    public function testNotEscaping(){
        $ps = new ParamStore();
        $w = Where::new('name', '=', '**email');
        $w->isFirst = true;

        $actual = $w->toString($ps);
        $expected = " WHERE `name` = `email`";
        $this->assertEquals($expected, $actual);
    }

    public function testNulls(){
        $ps = new ParamStore();
        $w = Where::new('name', '=', null);
        $w->isFirst = true;

        $actual = $w->toString($ps);
        $expected = " WHERE `name` IS NULL";
        $this->assertEquals($expected, $actual);
    }

    public function testNulls2(){
        $ps = new ParamStore();
        $w = Where::new('name', '!=', null);
        $w->isFirst = true;

        $actual = $w->toString($ps);
        $expected = " WHERE `name` IS NOT NULL";
        $this->assertEquals($expected, $actual);
    }

    public function testNulls3(){
        $ps = new ParamStore();
        $w = Where::new('name', 'IS', null);
        $w->isFirst = true;

        $actual = $w->toString($ps);
        $expected = " WHERE `name` IS NULL";
        $this->assertEquals($expected, $actual);
    }



}