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
use c00\QueryBuilder\WhereGroup;

class WhereGroupTest extends \PHPUnit_Framework_TestCase
{

    public function testBasicGroup(){

        $g = WhereGroup::new('name', '=', 'peter');

        $params = [];
        $g->setUniqueIds($params);

        $keys = array_keys($params);

        $expected = "(`name` = :{$keys[0]})";
        $this->assertEquals($expected, $g->toString());
    }

    public function testBasicGroup2(){

        $g = WhereGroup::new('name', '=', 'peter')
            ->where('email', '=', 'peter@blaat.com');

        $params = [];
        $g->setUniqueIds($params);

        $keys = array_keys($params);

        $expected = "(`name` = :{$keys[0]} AND `email` = :{$keys[1]})";
        $this->assertEquals($expected, $g->toString());
    }

    public function testBasicGroup3(){

        $g = WhereGroup::new('table.name', '=', 'peter')
            ->where('email', '=', 'peter@blaat.com')
            ->orWhere('id', '=', 1);

        $params = [];
        $g->setUniqueIds($params);

        $keys = array_keys($params);

        $expected = "(`table`.`name` = :{$keys[0]} AND `email` = :{$keys[1]} OR `id` = :{$keys[2]})";
        $this->assertEquals($expected, $g->toString());
    }


}