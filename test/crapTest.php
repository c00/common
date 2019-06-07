<?php

namespace test;


use c00\common\CovleDate;
use PHPUnit\Framework\TestCase;

class crapTest extends TestCase
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
        //$this->assertSame("lol" - 2, -2); // Non numeric value error
        //$this->assertSame("Anything" - 1, -1); // Non numerif value error
        $this->assertSame("0" + 4, 4);
        $this->assertSame("0" . 4, "04");
        $this->assertSame("0" - 4, -4);
        $this->assertSame("0" * 4, 0);
        $this->assertSame("0" / 4, 0);
        //$this->assertSame("0" / 0, 0); //Division by Zero exception
        //$this->assertSame("foo" - "bar", 0); //Non numeric error
        //$this->assertNotSame("foo" - "bar", 2); Non numeric value
        //$this->assertNotSame("foo" - "bar", false); //Non numeric value
        //$this->assertSame("foo" * "bar", 0); //Non numeric value
        //$this->assertSame("foo" / "bar", 0); //Division by Zero exception

    }

    public function testNothing(){

        $now = CovleDate::now();

        $past = $now->cloneDate()->addMinutes(-70);

        $diff = $past->diff($now);

        $this->assertEquals(14, ceil(13.3));
        $this->assertEquals(14, ceil(13.00001));
        $this->assertEquals(14, ceil(13.9));
        $this->assertEquals(14, ceil(14));



    }

}