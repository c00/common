<?php


namespace test;

use c00\QueryBuilder\components\FromClass;
use c00\QueryBuilder\components\SelectClause;
use c00\sample\User;
use PHPUnit\Framework\TestCase;

class selectClauseTest extends TestCase
{

    public function testBasic(){
        $clause = new SelectClause();

        $clause->addColumn('*');
        $f = FromClass::newFromClass(User::class, 'user', 'u');

        $clause->addClassColumns($f, []);

        $this->assertEquals(5, count($clause->getColumns()));

        $string = "SELECT `u`.`id` AS `u.id`, `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage`";
        $this->assertEquals($string, $clause->toString());

    }

    public function testBasic2(){
        $clause = new SelectClause();

        //no columns
        $f = FromClass::newFromClass(User::class, 'user', 'u');

        $clause->addClassColumns($f, []);

        $this->assertEquals(5, count($clause->getColumns()));

        $string = "SELECT `u`.`id` AS `u.id`, `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage`";
        $this->assertEquals($string, $clause->toString());

    }

    public function testBasic3(){
        $clause = new SelectClause();

        $clause->addColumn('u.*');
        $clause->addColumn('w.*');
        $clause->addColumn('q.date');

        $f = FromClass::newFromClass(User::class, 'user', 'u');

        $clause->addClassColumns($f, []);

        $this->assertEquals(7, count($clause->getColumns()));

        $string = "SELECT `w`.*, `q`.`date`, `u`.`id` AS `u.id`, `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage`";
        $this->assertEquals($string, $clause->toString());

    }

    public function testBasic4(){
        $clause = new SelectClause();

        $clause->addColumn('u.*');
        $clause->addColumn('w.*');
        $clause->addColumn('q.date');

        $f = FromClass::newFromClass(User::class, 'user', 'u');

        $clause->addClassColumns(null, []);

        $this->assertEquals(3, count($clause->getColumns()));

        $string = "SELECT `u`.*, `w`.*, `q`.`date`";
        $this->assertEquals($string, $clause->toString());

        //$clause->addClassColumns($f, [things]);
    }


    public function testDuplicates(){
        $clause = new SelectClause();

        $clause->addColumn('u.name', 'u.name');
        $clause->addColumn('u.email', 'u.email');
        $clause->addColumn('u.*');

        $f = FromClass::newFromClass(User::class, 'user', 'u');

        $clause->addClassColumns($f, []);

        $this->assertEquals(5, count($clause->getColumns()));

        $string = "SELECT `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`id` AS `u.id`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage`";
        $this->assertEquals($string, $clause->toString());

        //$clause->addClassColumns($f, [things]);
    }

}