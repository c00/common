<?php

namespace test;


use c00\common\CovleDate;
use c00\sample\MappedTeam;
use c00\sample\User;
use PHPUnit\Framework\TestCase;

class DatabaseObjectTest extends TestCase
{
    const TEST_DATE_SECONDS = 507303000;

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
        $this->assertEquals("float", $a['code']);
        $this->assertEquals("bool", $a['active']);
    }


    public function testFromArray(){
        $input = [
            'TEAMID' => '12',
            'TEAMNAME' => 'Dudemeisters',
            'TEAMCODE' => "12345.9",
            'TEAMSTATUS' => "1",
            'created' => self::TEST_DATE_SECONDS
        ];

        $team = MappedTeam::fromArray($input);

        //Test mapping
        $this->assertEquals(12, $team->id);
        $this->assertEquals(12345.9, $team->code);
        $this->assertEquals(1, $team->active);
        $this->assertEquals('Dudemeisters', $team->name);

        //Test datatypes
        $this->assertTrue(is_int($team->id));
        $this->assertTrue(is_float($team->code));
        $this->assertTrue(is_bool($team->active));
        $this->assertTrue($team->created instanceof CovleDate);
    }

    public function testToArray(){
        $team = new MappedTeam();
        $team->id = 12;
        $team->code = 12345.9;
        $team->active = true;
        $team->created = CovleDate::fromSeconds(self::TEST_DATE_SECONDS);
        $team->name = "Dudemeisters";

        $output = $team->toArray();

        //Check mapping
        $this->assertSame(12, $output['TEAMID']);
        $this->assertSame('Dudemeisters', $output['TEAMNAME']);
        $this->assertSame(12345.9, $output['TEAMCODE']);
        $this->assertSame(1, $output['TEAMSTATUS']);
        $this->assertSame(self::TEST_DATE_SECONDS, $output['created']);

        //Check datatypes
        $this->assertTrue(is_int($output['created']));
        $this->assertTrue(is_int($output['TEAMSTATUS']));
    }

    public function testRemoveInternalTypes(){
        $u = new User();
        $u->name = "Peter";
        $u->email = "peter@covle.com";

        $a = $u->toArray();
        $this->assertFalse(isset($a['_mapping']));
        $this->assertFalse(isset($a['_dataTypes']));

        $s = $u->toShowable();
        $this->assertFalse(isset($s['_mapping']));
        $this->assertFalse(isset($s['_dataTypes']));
    }

    public function testIgnoredFields(){
        $u = new User();
        $u->name = "Peter";
        $u->email = "peter@covle.com";
        $u->notADatabaseField = "penis";

        $a = $u->toArray();
        $this->assertTrue(isset($a['name']));
        $this->assertTrue(isset($a['email']));
        $this->assertFalse(isset($a['notADatabaseField']));
    }
}