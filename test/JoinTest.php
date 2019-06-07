<?php

namespace test;

use c00\QueryBuilder\components\Join;
use c00\QueryBuilder\components\JoinClass;
use c00\QueryBuilder\ParamStore;
use c00\sample\User;
use PHPUnit\Framework\TestCase;

class JoinTest extends TestCase
{

    public function testBasic(){
        $join = Join::newJoin('user', 'u', 'u.id', '=', 's.userId');
        $expected = " JOIN `user` AS `u` ON `u`.`id` = `s`.`userId`";
        $this->assertEquals($expected, $join->toString());
    }

    public function testBasic2(){
        $join = Join::newJoin('user', null, 'user.id', '=', 's.userId');
        $expected = " JOIN `user` ON `user`.`id` = `s`.`userId`";
        $this->assertEquals($expected, $join->toString());
    }

    public function testOuter(){
        $join = Join::newOuterJoin('user', null, 'user.id', '=', 's.userId');
        $expected = " LEFT OUTER JOIN `user` ON `user`.`id` = `s`.`userId`";
        $this->assertEquals($expected, $join->toString());

        $join = Join::newOuterJoin('user', 'u', 'u.id', '=', 's.userId', 'RIGHT');
        $expected = " RIGHT OUTER JOIN `user` AS `u` ON `u`.`id` = `s`.`userId`";
        $this->assertEquals($expected, $join->toString());
    }

	public function testMultiJoin() {
		$join = Join::newJoin('user', null, 'user.id', '=', 's.userId')
		            ->andOn('user.group', '=', 's.group');

		$expected = " JOIN `user` ON `user`.`id` = `s`.`userId` AND `user`.`group` = `s`.`group`";
		$this->assertEquals($expected, $join->toString());

		$join = Join::newJoin('user', null, 'user.id', '=', 's.userId')
		            ->orOn('user.group', '=', 's.group');

		$expected = " JOIN `user` ON `user`.`id` = `s`.`userId` OR `user`.`group` = `s`.`group`";
		$this->assertEquals($expected, $join->toString());
	}

	public function testMultiJoinClass() {
		$join = JoinClass::newJoinClass(User::class, 'user', 'u', 'u.id', '=', 's.userId')
		                 ->andOn('u.group', '=', 's.group');

		$expected = " JOIN `user` AS `u` ON `u`.`id` = `s`.`userId` AND `u`.`group` = `s`.`group`";
		$this->assertEquals($expected, $join->toString());

		$join = Join::newJoin('user', null, 'user.id', '=', 's.userId')
		            ->orOn('user.group', '=', 's.group');

		$expected = " JOIN `user` ON `user`.`id` = `s`.`userId` OR `user`.`group` = `s`.`group`";
		$this->assertEquals($expected, $join->toString());
	}

	public function testMultiJoinClassOnValue() {
    	$join = JoinClass::newJoinClass(User::class, 'user', 'u', 'u.id', '=', 's.userId')
		                 ->andOn('u.group', '=', 'snickers', false);

		$ps = new ParamStore();
		$sql = $join->toString($ps);
		$params = array_keys($ps->getParams());

		$expected = " JOIN `user` AS `u` ON `u`.`id` = `s`.`userId` AND `u`.`group` = :{$params[0]}";
		$this->assertEquals($expected, $sql);

		$join = Join::newJoin('user', null, 'user.id', '=', 's.userId')
		            ->orOn('user.group', '=', 's.group', false);

		$ps = new ParamStore();
		$sql = $join->toString($ps);
		$params = array_keys($ps->getParams());

		$expected = " JOIN `user` ON `user`.`id` = `s`.`userId` OR `user`.`group` = :{$params[0]}";
		$this->assertEquals($expected, $sql);
	}

    public function testMultiOuter(){
        $join = Join::newOuterJoin('user', null, 'user.id', '=', 's.userId')
            ->andOn('user.group', '=', 's.group');

        $expected = " LEFT OUTER JOIN `user` ON `user`.`id` = `s`.`userId` AND `user`.`group` = `s`.`group`";
        $this->assertEquals($expected, $join->toString());

        $join = Join::newOuterJoin('user', 'u', 'u.id', '=', 's.userId', 'RIGHT')
            ->orOn('user.group', '=', 's.group');

        $expected = " RIGHT OUTER JOIN `user` AS `u` ON `u`.`id` = `s`.`userId` OR `user`.`group` = `s`.`group`";
        $this->assertEquals($expected, $join->toString());
    }


}