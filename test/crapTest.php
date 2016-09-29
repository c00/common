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
        $i0 = 0;

        $s1 = "1";
        $s0 = "0";
        $b1 = true;
        $b0 = false;

        $this->assertTrue((bool)$i1);
        $this->assertTrue((bool)$s1);
        $this->assertTrue((bool)$b1);

        $this->assertFalse((bool)$i0);
        $this->assertFalse((bool)$s0);
        $this->assertFalse((bool)$b0);
    }

    public function testSomething(){
        $d = CovleDate::now();
        $this->assertNotNull($d);
    }
}