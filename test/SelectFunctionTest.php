<?php

namespace test;

use c00\QueryBuilder\components\SelectFunction;
use PHPUnit\Framework\TestCase;

class SelectFunctionTest extends TestCase
{

    public function testBasic(){
        $gc = new SelectFunction('GROUP_CONCAT', 'name');

        $actual = $gc->toString();
        $expected = "GROUP_CONCAT(`name`)";

        $this->assertEquals($expected, $actual);
    }

    public function testBasic2(){
        $gc = new SelectFunction('GROUP_CONCAT', 'name', null, 'DISTINCT');

        $actual = $gc->toString();
        $expected = "GROUP_CONCAT(DISTINCT `name`)";

        $this->assertEquals($expected, $actual);
    }

    public function testBasic3(){
        $gc = new SelectFunction('GROUP_CONCAT', 'user.name', "theName");

        $actual = $gc->toString();
        $expected = "GROUP_CONCAT(`user`.`name`) AS `theName`";

        $this->assertEquals($expected, $actual);
    }

    public function testBasic4(){
        $gc = new SelectFunction('GROUP_CONCAT', 'user.name', "theName", 'DISTINCT');

        $actual = $gc->toString();
        $expected = "GROUP_CONCAT(DISTINCT `user`.`name`) AS `theName`";

        $this->assertEquals($expected, $actual);
    }

	public function testGroupConcat(){
		$gc = new SelectFunction('GROUP_CONCAT', 'user.name', "theName", 'DISTINCT', ['SEPARATOR' => ', ']);

		$actual = $gc->toString();
		$expected = "GROUP_CONCAT(DISTINCT `user`.`name` SEPARATOR ', ') AS `theName`";

		$this->assertEquals($expected, $actual);
	}


}