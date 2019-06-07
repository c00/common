<?php

namespace test;

use c00\QueryBuilder\components\JoinClass;
use c00\QueryBuilder\components\Select;
use c00\sample\MappedTeam;
use c00\sample\User;
use PHPUnit\Framework\TestCase;

class JoinClassTest extends TestCase
{

    public function testBasic(){

        $f = JoinClass::newJoinClass(User::class,'db.user', 'u', 'u.id', '=', 's.userId');
        $expected = " JOIN `db`.`user` AS `u` ON `u`.`id` = `s`.`userId`";
        $this->assertEquals($expected, $f->toString());
    }

    public function testClassWithIgnored() {
        $f = JoinClass::newJoinClass(User::class,'user', 'u', 'u.id', '=', 's.userId');

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
        $f = JoinClass::newJoinClass(MappedTeam::class, 'user', 'u', 'u.id', '=', 's.userId');

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