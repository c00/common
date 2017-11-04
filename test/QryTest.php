<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 17/06/2016
 * Time: 11:03
 */

use c00\QueryBuilder\Qry;
use c00\QueryBuilder\QueryBuilderException;
use c00\QueryBuilder\components\Ranges;
use c00\QueryBuilder\components\WhereGroup;
use c00\sample\Session;
use c00\sample\User;

class QryTest extends PHPUnit_Framework_TestCase{

    public function testSelect(){
        $expected = "SELECT * FROM `user`";

        $query = Qry::select()->from('user');

        $this->assertSame($expected, $query->getSql());

    }

    public function testSelectEncapped(){
        $expected = "SELECT * FROM `table`.`user`";

        $query = Qry::select()->from('table.user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testOrderBy(){
        $expected = "SELECT * FROM `user` ORDER BY `user`.`name` ASC";

        $query = Qry::select()->from('user')
            ->orderBy('user.name');

        $this->assertSame($expected, $query->getSql());

    }

    public function testOrderBy2(){

        $query = Qry::select()->from('user')
            ->where('role', '=', 'admin')
            ->orderBy('user.name', false)
            ->limit(15);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` WHERE `role` = :{$key} ORDER BY `user`.`name` DESC LIMIT 15";

        $this->assertSame($expected, $sql);

    }

    public function testSelectMax(){
        $expected = "SELECT `email`, MAX(`id`) FROM `user`";

        $query = Qry::select('email')
            ->max('id')
            ->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectMax2(){
        $expected = "SELECT MAX(`id`) FROM `user`";

        $query = Qry::select()
            ->max('id')
            ->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectMax3(){
        $expected = "SELECT `challengeId`, `code`, `image`, `created`, `correct`, MAX(`created`) FROM `answer`";

        $query = Qry::select(['challengeId', 'code', 'image', 'created', 'correct' ])
            ->max('created')
            ->from('answer');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectMin(){
        $expected = "SELECT `email`, MIN(`id`) FROM `user`";

        $query = Qry::select('email')
            ->min('id')
            ->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectMinWithKeyword(){
        $expected = "SELECT `email`, MIN(DISTINCT `id`) FROM `user`";

        $query = Qry::select('email')
            ->min('id', null, 'DISTINCT')
            ->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectMin2(){
        $expected = "SELECT MIN(`id`) FROM `user`";

        $query = Qry::select()
            ->min('id')
            ->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectMin3(){
        $expected = "SELECT `challengeId`, `code`, `image`, `created`, `correct`, MIN(`created`) FROM `answer`";

        $query = Qry::select(['challengeId', 'code', 'image', 'created', 'correct' ])
            ->min('created')
            ->from('answer');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectSum(){
        $expected = "SELECT `email`, SUM(`id`) FROM `user`";

        $query = Qry::select('email')
            ->sum('id')
            ->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectSum2(){
        $expected = "SELECT SUM(`id`) FROM `user`";

        $query = Qry::select()
            ->sum('id')
            ->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectSum3(){
        $expected = "SELECT `challengeId`, `code`, `image`, `created`, `correct`, SUM(`created`) FROM `answer`";

        $query = Qry::select(['challengeId', 'code', 'image', 'created', 'correct' ])
            ->sum('created')
            ->from('answer');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectWhere(){
        $query = Qry::select()->from('user')->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereIn(){
        $query = Qry::select()->from('user')->whereIn('id', [1, 6, 8]);

        $params = [];
        $sql = $query->getSql($params);
        $keys = array_keys($params);

        $expected = "SELECT * FROM `user` WHERE `id` IN (:{$keys[0]}, :{$keys[1]}, :{$keys[2]})";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereIn2(){
        $query = Qry::select()
            ->from('user')
            ->where('email', '=', 'coo@covle.com')
            ->whereIn('id', [1, 6, 8]);

        $params = [];
        $sql = $query->getSql($params);
        $keys = array_keys($params);

        $expected = "SELECT * FROM `user` WHERE `email` = :{$keys[0]} AND `id` IN (:{$keys[1]}, :{$keys[2]}, :{$keys[3]})";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereIn3(){
        $query = Qry::select()->from('user')->whereIn('id', []);

        $params = [];
        $sql = $query->getSql($params);

        $expected = "SELECT * FROM `user`";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereIn4(){
        $query = Qry::select()
            ->from('user')
            ->where('email', '=', 'coo@covle.com')
            ->whereIn('id', []);

        $params = [];
        $sql = $query->getSql($params);
        $keys = array_keys($params);

        $expected = "SELECT * FROM `user` WHERE `email` = :{$keys[0]}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereNotIn(){
        $query = Qry::select()->from('user')->whereNotIn('id', [1, 6, 8]);

        $params = [];
        $sql = $query->getSql($params);
        $keys = array_keys($params);

        $expected = "SELECT * FROM `user` WHERE `id` NOT IN (:{$keys[0]}, :{$keys[1]}, :{$keys[2]})";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereNotIn2(){
        $query = Qry::select()->from('user')
            ->whereIn('id', [2, 4, 6])
            ->whereNotIn('id', [1, 3, 5]);

        $params = [];
        $sql = $query->getSql($params);
        $keys = array_keys($params);

        $expected = "SELECT * FROM `user` WHERE `id` IN (:{$keys[0]}, :{$keys[1]}, :{$keys[2]}) AND `id` NOT IN (:{$keys[3]}, :{$keys[4]}, :{$keys[5]})";

        $this->assertSame($expected, $sql);
    }

    public function testSelectDistinctWhere(){
        $query = Qry::select([], true)->from('user')->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT DISTINCT * FROM `user` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectDistinctWhere2(){
        $query = Qry::select(['id', 'name'], true)->from('user')->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT DISTINCT `id`, `name` FROM `user` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhere2(){
        $params = [];
        $sql  = Qry::select()
            ->from('user')
            ->where('id', '=', 1)
            ->where('email', '=', 'coo@covle.com')
            ->where('role', '>', '3')
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $emailKey = $keys[1];
        $roleKey = $keys[2];

        $expected = "SELECT * FROM `user` WHERE `id` = :$idKey AND `email` = :$emailKey AND `role` > :$roleKey";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhere3(){
        $params = [];
        $sql  = Qry::select(['id', 'user.email', 'user.*'])
            ->from('user')
            ->where('id', '=', 1)
            ->where('email', '=', 'coo@covle.com')
            ->where('role', '>', '3')
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $emailKey = $keys[1];
        $roleKey = $keys[2];

        $expected = "SELECT `id`, `user`.`email`, `user`.* FROM `user` WHERE `id` = :$idKey AND `email` = :$emailKey AND `role` > :$roleKey";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhere4(){
        $params = [];
        $sql  = Qry::select(['id', 'emailAddress' => 'user.email'])
            ->from('user')
            ->where('id', '=', 1)
            ->where('email', '=', 'coo@covle.com')
            ->where('role', '>', '3')
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $emailKey = $keys[1];
        $roleKey = $keys[2];

        $expected = "SELECT `id`, `user`.`email` AS `emailAddress` FROM `user` WHERE `id` = :$idKey AND `email` = :$emailKey AND `role` > :$roleKey";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereNull(){
        $params = [];
        $sql  = Qry::select(['id', 'emailAddress' => 'user.email'])
            ->from('user')
            ->where('id', '=', 1)
            ->where('email', '=', 'coo@covle.com')
            ->where('role', '>', '3')
            ->where('species', 'IS', null)
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $emailKey = $keys[1];
        $roleKey = $keys[2];

        $expected = "SELECT `id`, `user`.`email` AS `emailAddress` FROM `user` WHERE `id` = :$idKey AND `email` = :$emailKey AND `role` > :$roleKey AND `species` IS NULL";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereLimit(){
        $params = [];
        $sql  = Qry::select()
            ->from('user')
            ->where('id', '=', 1)
            ->where('email', '=', 'coo@covle.com')
            ->where('role', '>', '3')
            ->limit(10)
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $emailKey = $keys[1];
        $roleKey = $keys[2];

        $expected = "SELECT * FROM `user` WHERE `id` = :$idKey AND `email` = :$emailKey AND `role` > :$roleKey LIMIT 10";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereLimitOffset(){
        $params = [];
        $sql  = Qry::select()
            ->from('user')
            ->where('id', '=', 1)
            ->where('email', '=', 'coo@covle.com')
            ->where('role', '>', '3')
            ->limit(10, 20)
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $emailKey = $keys[1];
        $roleKey = $keys[2];

        $expected = "SELECT * FROM `user` WHERE `id` = :$idKey AND `email` = :$emailKey AND `role` > :$roleKey LIMIT 10 OFFSET 20";

        $this->assertSame($expected, $sql);
    }

    public function testCheckDataType(){
        $s = "bla";
        $qb = new Qry();
        //Check simple types
        $this->assertTrue($qb->checkDataType($s, ['object', 'string']));
        $this->assertTrue($qb->checkDataType($s, 'string'));

        //Check classes
        $o = new \c00\common\CovleDate();
        $this->assertTrue($qb->checkDataType($o, \c00\common\CovleDate::class));

        //Check if exception is thrown if it's not ok.
        try {
            $this->assertTrue($qb->checkDataType($s, ['object', 'int']));
            $this->fail("Exception expected.");
        } catch (QueryBuilderException $e){
            $this->assertSame($e->getCode(), 10);
        }

        //Check interface implementation
        $t = new \c00\sample\Team();
        $this->assertTrue($qb->checkDataType($t, \c00\common\IDatabaseObject::class));

    }

    public function testUpdate(){
        $params = [];
        $t = new \c00\sample\Team();
        $t->active = 1;
        $t->code = "teamcode";
        $t->name = "teamname";

        $q = Qry::update('user', $t);
        $actual = $q->getSql($params);
        $keys = array_keys($params);
        $nameKey = $keys[0];
        $codeKey = $keys[1];
        $activeKey = $keys[2];

        //Test without WHERE
        $expected = "UPDATE `user` SET `name` = :$nameKey, `code` = :$codeKey, `active` = :$activeKey";
        $this->assertEquals($expected, $actual);

        //Test with normal WHERE
        $q->where('code', '=', 123);

        $actual = $q->getSql($params);
        $keys = array_keys($params);
        $expected = "UPDATE `user` SET `name` = :{$keys[0]}, `code` = :{$keys[1]}, `active` = :$keys[2] WHERE `code` = :{$keys[3]}";

        $this->assertEquals($expected, $actual);

        //Two WHERES
        $q->where('name', '=', 'barbers');

        $actual = $q->getSql($params);
        $keys = array_keys($params);
        $expected = "UPDATE `user` SET `name` = :{$keys[0]}, `code` = :{$keys[1]}, `active` = :$keys[2] WHERE `code` = :{$keys[3]} AND `name` = :{$keys[4]}";

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateJoin(){
        $params = [];
        $t = new \c00\sample\Team();
        $t->active = 1;
        $t->code = "teamcode";
        $t->name = "teamname";

        //note: I don't know the alias for the columns, so this might have unexpected real world consequences. Use an array instead.

        $q = Qry::update(['u' => 'user'], $t)
            ->where('u.code', '=', 123);

        $actual = $q->getSql($params);
        $keys = array_keys($params);
        $expected = "UPDATE `user` AS `u` SET `name` = :{$keys[0]}, `code` = :{$keys[1]}, `active` = :$keys[2] WHERE `u`.`code` = :{$keys[3]}";

        $this->assertEquals($expected, $actual);

        //With JOIN
        $q->join(['c' => 'challenge'], 't.challengeId', '=', 'c.id');

        $actual = $q->getSql($params);
        $keys = array_keys($params);
        $expected = "UPDATE `user` AS `u` JOIN `challenge` AS `c` ON `t`.`challengeId` = `c`.`id` SET `name` = :{$keys[0]}, `code` = :{$keys[1]}, `active` = :$keys[2] WHERE `u`.`code` = :{$keys[3]}";

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateJoinWithArray(){
        $params = [];
        $updateBody = [
            'u.name' => 'karel',
            'u.code' => '666'
        ];

        $q = Qry::update(['u' => 'user'], $updateBody)
            ->where('u.code', '=', 123);

        $actual = $q->getSql($params);
        $keys = array_keys($params);
        $expected = "UPDATE `user` AS `u` SET `u`.`name` = :{$keys[0]}, `u`.`code` = :{$keys[1]} WHERE `u`.`code` = :{$keys[2]}";

        $this->assertEquals($expected, $actual);

        //With JOIN
        $q->join(['c' => 'challenge'], 't.challengeId', '=', 'c.id');

        $actual = $q->getSql($params);
        $keys = array_keys($params);
        $expected = "UPDATE `user` AS `u` JOIN `challenge` AS `c` ON `t`.`challengeId` = `c`.`id` SET `u`.`name` = :{$keys[0]}, `u`.`code` = :{$keys[1]} WHERE `u`.`code` = :{$keys[2]}";

        $this->assertEquals($expected, $actual);
    }

    public function testUpdateWhereIn(){
        $params = [];
        $t = new \c00\sample\Team();
        $t->active = 1;
        $t->code = "teamcode";
        $t->name = "teamname";

        $idArray = [2,4,6];

        $q = Qry::update('user', $t)
            ->whereIn('id', $idArray);

        $actual = $q->getSql($params);
        $keys = array_keys($params);
        $nameKey = $keys[0];
        $codeKey = $keys[1];
        $activeKey = $keys[2];

        //Test without WHERE
        $expected = "UPDATE `user` SET `name` = :$nameKey, `code` = :$codeKey, `active` = :$activeKey WHERE `id` IN (:{$keys[3]}, :{$keys[4]}, :{$keys[5]})";
        $this->assertEquals($expected, $actual);
    }

    public function testInsert(){
        $params = [];
        $t = new \c00\sample\Team();
        $t->active = 1;
        $t->code = "teamcode";
        $t->name = "teamname";

        $q = Qry::insert('user', $t);
        $actual = $q->getSql($params);
        $keys = array_keys($params);

        $expected = "INSERT INTO `user` (`name`, `code`, `active`) VALUES(:$keys[0], :$keys[1], :$keys[2])";
        $this->assertEquals($expected, $actual);
    }

    public function testInsertArray(){
        $params = [];
        $a = [
            'name' => 'karel',
            'code' => '123',
            'active' => 1
        ];

        $q = Qry::insert('user', $a);
        $actual = $q->getSql($params);
        $keys = array_keys($params);

        $expected = "INSERT INTO `user` (`name`, `code`, `active`) VALUES(:$keys[0], :$keys[1], :$keys[2])";
        $this->assertEquals($expected, $actual);
    }

    public function testDelete(){
        $expected = "DELETE FROM `user`";

        $query = Qry::delete()->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testDelete2(){
        $expected = "DELETE FROM `user`";

        $query = Qry::delete('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testDelete3(){
        $expected = "DELETE FROM `user`";

        $query = Qry::delete('user');

        $this->assertSame($expected, $query->getSql());
    }


    public function testDeleteWhere(){
        $query = Qry::delete()
            ->from('user')
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "DELETE FROM `user` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testDeleteWhereIn(){
        $query = Qry::delete()
            ->from('user')
            ->whereIn('id', [1, 2, 4]);

        $params = [];
        $sql = $query->getSql($params);
        $keys = array_keys($params);

        $expected = "DELETE FROM `user` WHERE `id` IN (:{$keys[0]}, :{$keys[1]}, :{$keys[2]})";

        $this->assertSame($expected, $sql);
    }

    public function testDeleteJoin(){
        $query = Qry::delete()
            ->from('user')
            ->join('session', 'session.userId', '=', 'user.id')
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];
        $expected = "DELETE `user` FROM `user` JOIN `session` ON `session`.`userId` = `user`.`id` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testDeleteJoin2(){
        $query = Qry::delete()
            ->from(['user', 'session'])
            ->join('session', 'session.userId', '=', 'user.id')
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];
        $expected = "DELETE `user`, `session` FROM `user`, `session` JOIN `session` ON `session`.`userId` = `user`.`id` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testDeleteJoin3(){
        $query = Qry::delete()
            ->from(['u' => 'user', 's' => 'session'])
            ->join('session', 'session.userId', '=', 'user.id')
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];
        $expected = "DELETE `u`, `s` FROM `user` AS `u`, `session` AS `s` JOIN `session` ON `session`.`userId` = `user`.`id` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testDeleteJoin4(){
        $query = Qry::delete()
            ->from(['u' => 'user'])
            ->join('session', 'session.userId', '=', 'user.id')
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];
        $expected = "DELETE `u` FROM `user` AS `u` JOIN `session` ON `session`.`userId` = `user`.`id` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectJoin(){
        $query = Qry::select()
            ->from('user')
            ->join('session', 'session.userId', '=', 'user.id')
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` JOIN `session` ON `session`.`userId` = `user`.`id` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }



    public function testSelectJoin2(){
        $query = Qry::select()
            ->from('user')
            ->join('session', '`session`.`userId`', '=', 'user.id')
            ->join('role', 'user.roleId', '=', 'role.id')
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` JOIN `session` ON `session`.`userId` = `user`.`id` JOIN `role` ON `user`.`roleId` = `role`.`id` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectOuterJoin(){
        $query = Qry::select()
            ->from('user')
            ->outerJoin('session', 'session.userId', '=', 'user.id')
            ->join('role', 'user.roleId', '=', 'role.id')
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` LEFT OUTER JOIN `session` ON `session`.`userId` = `user`.`id` JOIN `role` ON `user`.`roleId` = `role`.`id` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectOuterJoin2(){
        $query = Qry::select()
            ->from('user')
            ->outerJoin('session', 'session.userId', '=', 'user.id', "RIGHT")
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` RIGHT OUTER JOIN `session` ON `session`.`userId` = `user`.`id` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectOuterJoin3(){
        $query = Qry::select()
            ->from('user')
            ->outerJoin(['sesh' => 'session'], 'session.userId', '=', 'user.id', "RIGHT")
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` RIGHT OUTER JOIN `session` AS `sesh` ON `session`.`userId` = `user`.`id` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testWhereEncapped(){
        $query = Qry::select()
            ->from('user')
            ->join('session', 'session.userId', '=', 'user.id')
            ->where('session.id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` JOIN `session` ON `session`.`userId` = `user`.`id` WHERE `session`.`id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectFunctions(){
        $query = Qry::select()
            ->selectFunction("avg", "cost")
            ->from("product");

        $expected = "SELECT avg(`cost`) FROM `product`";

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectFunctionWithKeyword(){
        $query = Qry::select()
            ->selectFunction("avg", "cost", null, 'DISTINCT')
            ->from("product");

        $expected = "SELECT avg(DISTINCT `cost`) FROM `product`";

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectFunctions2(){
        $query = Qry::select("user")
            ->selectFunction("avg", "cost")
            ->selectFunction("max", "age")
            ->from("product");

        //Note, this is hardly valid without a GROUP BY...
        $expected = "SELECT `user`, avg(`cost`), max(`age`) FROM `product`";

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectFunctionsWithAlias(){
        $query = Qry::select("user")
            ->selectFunction("avg", "cost", "your face")
            ->from("product");

        //Note, this is hardly valid without a GROUP BY...
        $expected = "SELECT `user`, avg(`cost`) AS `your face` FROM `product`";

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectFunctionsWithAliasAndKeyword(){
        $query = Qry::select("user")
            ->selectFunction("avg", "cost", "your face", 'DISTINCT')
            ->from("product");

        //Note, this is hardly valid without a GROUP BY...
        $expected = "SELECT `user`, avg(DISTINCT `cost`) AS `your face` FROM `product`";

        $this->assertSame($expected, $query->getSql());
    }

    public function testNullIsNot0(){
        $q = Qry::select()
            ->from('user')
            ->where('status', '=', 0);

        $params = [];
        $sql = $q->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` WHERE `status` = :{$key}";
        $this->assertEquals($expected, $sql);
    }

    public function testFromAlias(){
        $q = Qry::select()->from(['u' => 'user']);
        $expected = "SELECT * FROM `user` AS `u`";

        $this->assertEquals($expected, $q->getSql());
    }

    public function testJoinAlias(){
        $q = Qry::select()
            ->from(['u' => 'user'])
            ->join (['g' => 'group'], "u.groupId", '=', "g.id");

        $expected = "SELECT * FROM `user` AS `u` JOIN `group` AS `g` ON `u`.`groupId` = `g`.`id`";

        $this->assertEquals($expected, $q->getSql());
    }

    public function testDontEscape(){
        $q = Qry::select()
            ->from(['u' => 'user'])
            ->where('birthday', '>', '**lastseen');

        $expected = "SELECT * FROM `user` AS `u` WHERE `birthday` > `lastseen`";

        $this->assertEquals($expected, $q->getSql());
    }

    public function testGroupBy(){
        $q = Qry::select('l.*')
            ->count('m.location', 'count')
            ->avg('m.happiness', 'avg')
            ->from(['m' => 'main'])
            ->join(['l' => 'location'], 'l.id', '=', 'm.locationId')
            ->groupBy('m.location');

        $expected = "SELECT `l`.*, COUNT(`m`.`location`) AS `count`, AVG(`m`.`happiness`) AS `avg` FROM `main` AS `m` JOIN `location` AS `l` ON `l`.`id` = `m`.`locationId` GROUP BY `m`.`location`";
        $this->assertEquals($expected, $q->getSql());
    }

    public function testHaving(){
        $q = Qry::select('l.*')
            ->count('m.location', 'count')
            ->avg('m.happiness', 'avg')
            ->from(['m' => 'main'])
            ->join(['l' => 'location'], 'l.id', '=', 'm.locationId')
            ->groupBy('m.location')
            ->having('count', '>', 10);

        $params = [];
        $sql = $q->getSql($params);
        $id = array_keys($params)[0];

        $expected = "SELECT `l`.*, COUNT(`m`.`location`) AS `count`, AVG(`m`.`happiness`) AS `avg` FROM `main` AS `m` JOIN `location` AS `l` ON `l`.`id` = `m`.`locationId` GROUP BY `m`.`location` HAVING `count` > :$id";
        $this->assertEquals($expected, $sql);
    }

    public function testGroupByRanges(){
        $ranges =  Ranges::newRanges('startTime', 'period');

        $ranges->addCaseLessThan('early', 50);
        $ranges->addCaseBetween('normal', 50, 100);
        $ranges->addCaseGreaterThan('late',100);


        $q = Qry::selectRange($ranges)
            ->count('startTime', 'Count')
            ->from('user');

        $params = [];
        $sql = $q->getSql($params);
        $keys = array_keys($params);

        $expected = "SELECT CASE WHEN `startTime` < :{$keys[0]} THEN 'early' WHEN `startTime` BETWEEN :{$keys[1]} AND :{$keys[2]} THEN 'normal' WHEN `startTime` > :{$keys[3]} THEN 'late' END AS `period`, COUNT(`startTime`) AS `Count` FROM `user` GROUP BY `period`";
        $this->assertEquals($expected, $sql);

    }

    public function testWhereCount(){
        $q = Qry::select()
            ->from('user');

        $this->assertEquals(0, $q->whereCount());

        $q->where('id', '=', 1);
        $this->assertEquals(1, $q->whereCount());

        $q->where('name', '=', 'peter');
        $this->assertEquals(2, $q->whereCount());

        $q->whereIn('month', [1, 4, 7, 9]);
        $this->assertEquals(3, $q->whereCount());
    }

    public function testFunctionWithOperator(){
        $query = Qry::select("id")
            ->avg('user.end - user.start')
            ->from("user");

        //Note, this is hardly valid without a GROUP BY...
        $expected = "SELECT `id`, AVG(`user`.`end` - `user`.`start`) FROM `user`";

        $this->assertSame($expected, $query->getSql());
    }

    public function testOr(){
        $params = [];
        $sql  = Qry::select()
            ->from('user')
            ->where('id', '=', 1)
            ->orWhere('email', '=', 'coo@covle.com')
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $emailKey = $keys[1];

        $expected = "SELECT * FROM `user` WHERE `id` = :$idKey OR `email` = :$emailKey";

        $this->assertSame($expected, $sql);
    }

    public function testOr2(){
        $params = [];
        $sql  = Qry::select()
            ->from('user')
            ->where('id', '=', 1)
            ->orWhere('email', '=', 'coo@covle.com')
            ->orWhere('email', '=', 'peter@example.com')
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $emailKey = $keys[1];
        $emailKey2 = $keys[2];

        $expected = "SELECT * FROM `user` WHERE `id` = :$idKey OR `email` = :$emailKey OR `email` = :$emailKey2";

        $this->assertSame($expected, $sql);
    }

    public function testOr3(){
        $params = [];
        $sql  = Qry::select()
            ->from('user')
            ->where('id', '=', 1)
            ->orWhere('email', '=', 'coo@covle.com')
            ->where('age', 'IS', null)
            ->orWhere('email', '=', 'peter@example.com')
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $emailKey = $keys[1];
        $emailKey2 = $keys[2];

        $expected = "SELECT * FROM `user` WHERE `id` = :$idKey OR `email` = :$emailKey AND `age` IS NULL OR `email` = :$emailKey2";

        $this->assertSame($expected, $sql);
    }

    public function testWhereGroup(){
        $params = [];
        $sql  = Qry::select()
            ->from('user')
            ->whereGroup(
                WhereGroup::new('id', '=', 1)
                    ->where('email', 'IS', null))
            ->orWhereGroup(
                WhereGroup::new('id', '!=', 1)
                    ->where('email', '=', 'coo@covle.com'))
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $idKey2 = $keys[1];
        $emailKey = $keys[2];
        $expected = "SELECT * FROM `user` WHERE (`id` = :$idKey AND `email` IS NULL) OR (`id` != :$idKey2 AND `email` = :$emailKey)";

        $this->assertSame($expected, $sql);
    }

    public function testWhereGroup2(){
        $params = [];
        $sql  = Qry::select()
            ->from('user')
            ->whereGroup(
                WhereGroup::new('id', '=', 1)
                    ->orWhere('email', 'IS', null))
            ->whereGroup(
                WhereGroup::new('id', '!=', 1)
                    ->where('email', '=', 'coo@covle.com'))
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $idKey2 = $keys[1];
        $emailKey = $keys[2];
        $expected = "SELECT * FROM `user` WHERE (`id` = :$idKey OR `email` IS NULL) AND (`id` != :$idKey2 AND `email` = :$emailKey)";

        $this->assertSame($expected, $sql);
    }

    public function testWhereGroup3(){
        $params = [];
        $sql  = Qry::select()
            ->from('user')
            ->where('date', '=', null)
            ->whereGroup(
                WhereGroup::new('id', '=', 1)
                    ->where('email', 'IS', null))
            ->orWhereGroup(
                WhereGroup::new('id', '!=', 1)
                    ->where('email', '=', 'coo@covle.com'))
            ->getSql($params);

        $keys = array_keys($params);
        $idKey = $keys[0];
        $idKey2 = $keys[1];
        $emailKey = $keys[2];
        $expected = "SELECT * FROM `user` WHERE `date` IS NULL AND (`id` = :$idKey AND `email` IS NULL) OR (`id` != :$idKey2 AND `email` = :$emailKey)";

        $this->assertSame($expected, $sql);
    }

    public function testGroupConcat() {
        $expected = "SELECT GROUP_CONCAT(DISTINCT `hobby`) FROM `table`.`user`";

        $query = Qry::select()
            ->groupConcat('hobby', null, 'DISTINCT')
            ->from('table.user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testFromClass() {
        /*
         * The query builder will add all User columns to the SELECT here itself,
         * because there's no columns defined and fromClass is used.
         */
        $q = Qry::select()
            ->fromClass(User::class, 'user', 'u')
            ->where('name', '=', 'little finger');

        $params = [];
        $actual = $q->getSql($params);
        $keys = array_keys($params);

        $expected = "SELECT `u`.`id` AS `u.id`, `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage` FROM `user` AS `u` WHERE `name` = :{$keys[0]}";

        $this->assertEquals($expected, $actual);
    }

    public function testFromAndJoinClass() {
        /*
         * The query builder will add all User and Session columns to the SELECT here itself,
         * because there's no columns defined and fromClass is used.
         */
        $q = Qry::select()
            ->fromClass(User::class, 'user', 'u')
            ->joinClass(Session::class, 'session', 's', 's.userId', '=', 'u.id')
            ->where('name', '=', 'little finger');

        $params = [];
        $actual = $q->getSql($params);
        $keys = array_keys($params);

        $expected = "SELECT `s`.`id` AS `s.id`, `s`.`userId` AS `s.userId`, `s`.`token` AS `s.token`, `s`.`expires` AS `s.expires`, `u`.`id` AS `u.id`, `u`.`name` AS `u.name`, `u`.`email` AS `u.email`, `u`.`active` AS `u.active`, `u`.`profileImage` AS `u.profileImage` FROM `user` AS `u` JOIN `session` AS `s` ON `s`.`userId` = `u`.`id` WHERE `name` = :{$keys[0]}";

        $this->assertEquals($expected, $actual);
    }

    public function testFromAndJoinClass2() {
        /*
         * The query builder will add all User and Session columns to the SELECT here itself,
         * because there's no columns defined and fromClass is used.
         */
        $q = Qry::select(['u.name', 's.*'])
            ->fromClass(User::class, 'user', 'u')
            ->joinClass(Session::class, 'session', 's', 's.userId', '=', 'u.id')
            ->where('name', '=', 'little finger');

        $params = [];
        $actual = $q->getSql($params);
        $keys = array_keys($params);

        $expected = "SELECT `u`.`name`, `s`.`id` AS `s.id`, `s`.`userId` AS `s.userId`, `s`.`token` AS `s.token`, `s`.`expires` AS `s.expires` FROM `user` AS `u` JOIN `session` AS `s` ON `s`.`userId` = `u`.`id` WHERE `name` = :{$keys[0]}";

        $this->assertEquals($expected, $actual);
    }
}