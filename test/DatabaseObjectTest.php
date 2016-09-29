<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 30/09/2016
 * Time: 00:38
 */

namespace test;


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
}