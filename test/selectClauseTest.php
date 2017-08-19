<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\QueryBuilder\components\From;
use c00\QueryBuilder\components\FromClass;
use c00\QueryBuilder\components\Select;
use c00\QueryBuilder\components\SelectClause;
use c00\sample\MappedTeam;
use c00\sample\User;

class selectClauseTest extends \PHPUnit_Framework_TestCase
{

    public function testBasic(){
        $clause = new SelectClause();

        $clause->columns[] = Select::new('*');
        $f = FromClass::new('user', 'u', User::class);

        $clause->addClassColumns($f, []);

        $this->assertEquals(5, count($clause->columns));

        $string = "SELECT `u`.`id` AS `u.id`, `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage`";
        $this->assertEquals($string, $clause->toString());

        //$clause->addClassColumns(null, []);
        //$clause->addClassColumns($f, [things]);
    }

    public function testBasic2(){
        $clause = new SelectClause();

        //no columns
        $f = FromClass::new('user', 'u', User::class);

        $clause->addClassColumns($f, []);

        $this->assertEquals(5, count($clause->columns));

        $string = "SELECT `u`.`id` AS `u.id`, `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage`";
        $this->assertEquals($string, $clause->toString());

        //$clause->addClassColumns(null, []);
        //$clause->addClassColumns($f, [things]);
    }

    public function testBasic3(){
        $clause = new SelectClause();

        $clause->columns[] = Select::new('u.*');
        $clause->columns[] = Select::new('w.*');
        $clause->columns[] = Select::new('q.date');

        $f = FromClass::new('user', 'u', User::class);

        $clause->addClassColumns($f, []);

        $this->assertEquals(7, count($clause->columns));

        $string = "SELECT `w`.*, `q`.`date`, `u`.`id` AS `u.id`, `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage`";
        $this->assertEquals($string, $clause->toString());

        //$clause->addClassColumns(null, []);
        //$clause->addClassColumns($f, [things]);
    }

}