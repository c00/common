<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;


use c00\common\CovleDate;

class crapTest extends \PHPUnit_Framework_TestCase
{

    public function testTruth(){
        $i1 = 1;
        $s1 = "1";
        $b1 = true;

        $i0 = 0;
        $s0 = "0";
        $b0 = false;

        $this->assertTrue((bool)$i1);
        $this->assertTrue((bool)$s1);
        $this->assertTrue((bool)$b1);

        $this->assertFalse((bool)$i0);
        $this->assertFalse((bool)$s0);
        $this->assertFalse((bool)$b0);
    }

    public function testTypes(){
        //assertSame(2, "2") will fail
        //assertEquals(2, "2") will pass

        //assertSame is strict. 1 != "1"
        $this->assertSame("lol" - 2, -2); // ???
        $this->assertSame("Anything" - 1, -1); // ???
        $this->assertSame("0" + 4, 4);
        $this->assertSame("0" . 4, "04");
        $this->assertSame("0" - 4, -4);
        $this->assertSame("0" * 4, 0);
        $this->assertSame("0" / 4, 0);
        //$this->assertSame("0" / 0, 0); //Division by Zero exception
        $this->assertSame("foo" - "bar", 0);
        $this->assertNotSame("foo" - "bar", 2);
        $this->assertNotSame("foo" - "bar", false);
        $this->assertSame("foo" * "bar", 0);
        //$this->assertSame("foo" / "bar", 0); //Division by Zero exception

    }

    public function testNothing(){
        $d = CovleDate::now()->addMonths(-7)->toSeconds();
        $d++;
    }

}