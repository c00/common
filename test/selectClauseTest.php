<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\QueryBuilder\components\FromClass;
use c00\QueryBuilder\components\SelectClause;
use c00\sample\User;

class selectClauseTest extends \PHPUnit_Framework_TestCase
{

    public function testBasic(){
        $clause = new SelectClause();

        $clause->addColumn('*');
        $f = FromClass::newFrom('user', 'u', User::class);

        $clause->addClassColumns($f, []);

        $this->assertEquals(5, count($clause->getColumns()));

        $string = "SELECT `u`.`id` AS `u.id`, `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage`";
        $this->assertEquals($string, $clause->toString());

    }

    public function testBasic2(){
        $clause = new SelectClause();

        //no columns
        $f = FromClass::newFrom('user', 'u', User::class);

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

        $f = FromClass::newFrom('user', 'u', User::class);

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

        $f = FromClass::newFrom('user', 'u', User::class);

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

        $f = FromClass::newFrom('user', 'u', User::class);

        $clause->addClassColumns($f, []);

        $this->assertEquals(5, count($clause->getColumns()));

        $string = "SELECT `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`id` AS `u.id`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage`";
        $this->assertEquals($string, $clause->toString());

        //$clause->addClassColumns($f, [things]);
    }

}