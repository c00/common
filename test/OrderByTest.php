<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\QueryBuilder\components\OrderBy;
use c00\QueryBuilder\components\Select;

class OrderByTest extends \PHPUnit_Framework_TestCase
{

    public function testBasic(){
        $o = OrderBy::new('name');
        $this->assertEquals('`name` ASC', $o->toString());

        $o = OrderBy::new('db.name');
        $this->assertEquals('`db`.`name` ASC', $o->toString());

        $o = OrderBy::new('name', false);
        $this->assertEquals('`name` DESC', $o->toString());

        $o = OrderBy::new('db.name', false);
        $this->assertEquals('`db`.`name` DESC', $o->toString());

        $o = OrderBy::new('name', true, 'IS NULL');
        $this->assertEquals('`name` IS NULL ASC', $o->toString());

        $o = OrderBy::new('name', false, 'IS NULL');
        $this->assertEquals('`name` IS NULL DESC', $o->toString());

        $o = OrderBy::new('db.name', false, 'IS NULL');
        $this->assertEquals('`db`.`name` IS NULL DESC', $o->toString());

        //Empty column will always result in nothing.
        $o = OrderBy::new('', false, 'IS NULL');
        $this->assertEquals('', $o->toString());

    }


}