<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\QueryBuilder\components\Join;
use c00\QueryBuilder\components\JoinClass;
use c00\sample\User;

class JoinTest extends \PHPUnit_Framework_TestCase
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