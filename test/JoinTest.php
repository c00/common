<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\QueryBuilder\components\Join;

class JoinTest extends \PHPUnit_Framework_TestCase
{

    public function testBasic(){
        $join = Join::newJoin('user', 'u', 'u.id', '=', 's.userId');
        $expected = " JOIN `user` `u` ON `u`.`id` = `s`.`userId`";
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
        $expected = " RIGHT OUTER JOIN `user` `u` ON `u`.`id` = `s`.`userId`";
        $this->assertEquals($expected, $join->toString());
    }


}