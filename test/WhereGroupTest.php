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
use c00\QueryBuilder\components\Comparison;
use c00\QueryBuilder\components\WhereGroup;
use c00\QueryBuilder\ParamStore;

class WhereGroupTest extends \PHPUnit_Framework_TestCase
{

    public function testBasicGroup(){

        $ps = new ParamStore();
        $g = WhereGroup::new('name', '=', 'peter');

        $g->isFirst = true;
        $actual = $g->toString($ps);
        $keys = array_keys($ps->getParams());

        $expected = " WHERE (`name` = :{$keys[0]})";
        $this->assertEquals($expected, $actual);

        $g->isFirst = false;
        $actual = $g->toString($ps);
        $keys = array_keys($ps->getParams());

        $expected = " AND (`name` = :{$keys[0]})";
        $this->assertEquals($expected, $actual);

        $g->type = Comparison::TYPE_OR;
        $actual = $g->toString($ps);
        $keys = array_keys($ps->getParams());

        $expected = " OR (`name` = :{$keys[0]})";
        $this->assertEquals($expected, $actual);
    }

    public function testBasicGroup2(){
        $ps = new ParamStore();
        $g = WhereGroup::new('name', '=', 'peter')
            ->where('email', '=', 'peter@blaat.com');

        $actual = $g->toString($ps);
        $keys = array_keys($ps->getParams());

        $expected = " AND (`name` = :{$keys[0]} AND `email` = :{$keys[1]})";
        $this->assertEquals($expected, $actual);
    }

    public function testBasicGroup3(){
        $ps = new ParamStore();
        $g = WhereGroup::new('table.name', '=', 'peter')
            ->where('email', '=', 'peter@blaat.com')
            ->orWhere('id', '=', 1);

        $actual = $g->toString($ps);
        $keys = array_keys($ps->getParams());

        $expected = " AND (`table`.`name` = :{$keys[0]} AND `email` = :{$keys[1]} OR `id` = :{$keys[2]})";
        $this->assertEquals($expected, $actual);
    }

    public function testWhereIn(){
        $ps = new ParamStore();
        $g = WhereGroup::new('table.name', '=', 'peter')
            ->where('email', '=', 'peter@blaat.com')
            ->orWhere('id', '=', 1)
            ->whereIn('name', ['peter', 'meg', 'steward']);

        $actual = $g->toString($ps);
        $keys = array_keys($ps->getParams());

        $expected = " AND (`table`.`name` = :{$keys[0]} AND `email` = :{$keys[1]} OR `id` = :{$keys[2]} AND `name` IN (:{$keys[3]}, :{$keys[4]}, :{$keys[5]}))";
        $this->assertEquals($expected, $actual);
    }

    public function testOrWhereIn(){
        $ps = new ParamStore();
        $g = WhereGroup::new('table.name', '=', 'peter')
            ->where('email', '=', 'peter@blaat.com')
            ->orWhere('id', '=', 1)
            ->orWhereIn('name', ['peter', 'meg', 'steward']);

        $actual = $g->toString($ps);
        $keys = array_keys($ps->getParams());

        $expected = " AND (`table`.`name` = :{$keys[0]} AND `email` = :{$keys[1]} OR `id` = :{$keys[2]} OR `name` IN (:{$keys[3]}, :{$keys[4]}, :{$keys[5]}))";
        $this->assertEquals($expected, $actual);
    }


}