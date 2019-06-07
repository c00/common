<?php

namespace test;

use c00\QueryBuilder\components\Select;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
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