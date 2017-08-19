<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\QueryBuilder\components\From;

class FromTest extends \PHPUnit_Framework_TestCase
{

    public function testBasic(){
        $f = From::new('user');
        $this->assertEquals('`user`', $f->toString());

        $f = From::new('db.user');
        $this->assertEquals('`db`.`user`', $f->toString());

        $f = From::new('user', 'userName');
        $this->assertEquals('`user` AS `userName`', $f->toString());

        $f = From::new('db.user', 'userName');
        $this->assertEquals('`db`.`user` AS `userName`', $f->toString());

        $f = From::new('db.user', 'u.userName');
        $this->assertEquals('`db`.`user` AS `u.userName`', $f->toString());
    }

    public function testTableName() {
        $f = From::new('db.table');
        $this->assertEquals('`db`.`table`', $f->getTableName());

        $f = From::new('table', 'dbName');
        $this->assertEquals('`dbName`', $f->getTableName());
    }


}