<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 30/09/2016
 * Time: 00:38
 */

namespace test;


use c00\common\CovleDate;
use c00\sample\MappedTeam;
use c00\sample\Team;
use c00\sample\User;

class DatabaseObjectTest extends \PHPUnit_Framework_TestCase
{
    public function interfaceTest(){

    }

    public function testAbstractDatabaseObject(){
        $u = new User();
        $u->name = "Peter";
        $u->email = "peter@covle.com";

        $uArray = $u->toArray();

        $u2 = User::fromArray($uArray);

        $this->assertEquals($u->name, $u2->name);
        $this->assertEquals($u->email, $u2->email);
        $this->assertTrue($u instanceof User);
        $this->assertTrue($u2 instanceof User);
    }

    public function testPropertyMapping(){
        $t = new MappedTeam();
        $a = $t->_getMapping();

        $this->assertEquals(4, count($a));

        $this->assertEquals("TEAMID", $a['id']);
        $this->assertEquals("TEAMNAME", $a['name']);
        $this->assertEquals("TEAMCODE", $a['code']);
        $this->assertEquals("TEAMSTATUS", $a['active']);
    }

    public function testPropertyTypes(){
        $t = new MappedTeam();
        $a = $t->_getDataTypes();

        $this->assertEquals(4, count($a));

        $this->assertEquals("int", $a['id']);
        $this->assertEquals(CovleDate::class, $a['created']);
        $this->assertEquals("double", $a['code']);
        $this->assertEquals("int", $a['active']);
    }
}