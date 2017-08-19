<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\QueryBuilder\components\Select;

class SelectTest extends \PHPUnit_Framework_TestCase
{

    public function testBasic(){
        $f = Select::new('firstName');
        $this->assertEquals('`firstName`', $f->toString());

        $f = Select::new('db.firstName');
        $this->assertEquals('`db`.`firstName`', $f->toString());

        $f = Select::new('firstName', 'userName');
        $this->assertEquals('`firstName` AS `userName`', $f->toString());

        $f = Select::new('db.firstName', 'userName');
        $this->assertEquals('`db`.`firstName` AS `userName`', $f->toString());

        $f = Select::new('db.firstName', 'u.userName');
        $this->assertEquals('`db`.`firstName` AS `u.userName`', $f->toString());
    }

    public function testString() {
        $f = Select::new('firstName', 'userName');

        $expected = $f->toString();
        $actual = (string) $f;

        $this->assertEquals($expected, $actual);
    }


}