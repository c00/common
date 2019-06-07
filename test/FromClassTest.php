<?php

namespace test;

use c00\QueryBuilder\components\FromClass;
use c00\QueryBuilder\components\Select;
use c00\sample\MappedTeam;
use c00\sample\User;
use PHPUnit\Framework\TestCase;

class FromClassTest extends TestCase
{

    public function testBasic(){

        $f = FromClass::newFromClass(User::class,'user', 'userName');
        $this->assertEquals('`user` AS `userName`', $f->toString());

        $f = FromClass::newFromClass(User::class,'db.user', 'userName');
        $this->assertEquals('`db`.`user` AS `userName`', $f->toString());

        $f = FromClass::newFromClass(User::class,'db.user', 'u.userName');
        $this->assertEquals('`db`.`user` AS `u.userName`', $f->toString());
    }

    public function testClassWithIgnored() {
        $f = FromClass::newFromClass(User::class,'user', 'u');

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
        $f = FromClass::newFromClass(MappedTeam::class, 'user', 'u');

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