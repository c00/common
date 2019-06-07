<?php

namespace test;

use c00\QueryBuilder\components\From;
use PHPUnit\Framework\TestCase;

class FromTest extends TestCase
{

    public function testBasic(){
        $f = From::newFrom('user');
        $this->assertEquals('`user`', $f->toString());

        $f = From::newFrom('db.user');
        $this->assertEquals('`db`.`user`', $f->toString());

        $f = From::newFrom('user', 'userName');
        $this->assertEquals('`user` AS `userName`', $f->toString());

        $f = From::newFrom('db.user', 'userName');
        $this->assertEquals('`db`.`user` AS `userName`', $f->toString());

        $f = From::newFrom('db.user', 'u.userName');
        $this->assertEquals('`db`.`user` AS `u.userName`', $f->toString());
    }

    public function testTableName() {
        $f = From::newFrom('db.table');
        $this->assertEquals('`db`.`table`', $f->getTableName());

        $f = From::newFrom('table', 'dbName');
        $this->assertEquals('`dbName`', $f->getTableName());
    }


}