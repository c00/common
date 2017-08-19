<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\QueryBuilder\components\FromClass;
use c00\QueryBuilder\components\Select;
use c00\sample\MappedTeam;
use c00\sample\User;

class FromClassTest extends \PHPUnit_Framework_TestCase
{

    public function testBasic(){

        $f = FromClass::new('user', 'userName', User::class);
        $this->assertEquals('`user` AS `userName`', $f->toString());

        $f = FromClass::new('db.user', 'userName', User::class);
        $this->assertEquals('`db`.`user` AS `userName`', $f->toString());

        $f = FromClass::new('db.user', 'u.userName', User::class);
        $this->assertEquals('`db`.`user` AS `u.userName`', $f->toString());
    }

    public function testClassWithIgnored() {
        $f = FromClass::new('user', 'u', User::class);

        $columns = $f->getClassColumns();
        $expected = [
            Select::new("u.id", "u.id"),
            Select::new("u.name", "u.name"),
            Select::new("u.email", "u.email"),
            Select::new("u.active", "u.active"),
            Select::new("u.profileImage", "u.profileImage")
        ];


        $this->assertEquals($expected, $columns);
    }

    public function testClassWithMapping() {
        $f = FromClass::new('user', 'u', MappedTeam::class);

        $columns = $f->getClassColumns();

        $expected = [
            Select::new("u.TEAMID", "u.TEAMID"),
            Select::new("u.TEAMNAME", "u.TEAMNAME"),
            Select::new("u.TEAMCODE", "u.TEAMCODE"),
            Select::new("u.TEAMSTATUS", "u.TEAMSTATUS"),
            Select::new("u.image", "u.image"),
            Select::new("u.created", "u.created")
        ];

        $this->assertEquals($expected, $columns);
    }

}