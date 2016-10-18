<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 17/06/2016
 * Time: 11:03
 */

use c00\QueryBuilder\Qry;
use c00\QueryBuilder\QueryBuilderException;

class QueryTest extends PHPUnit_Framework_TestCase
{
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

        $expected = "UPDATE `user` SET `name` = :$nameKey, `code` = :$codeKey, `active` = :$activeKey";
        $this->assertEquals($expected, $actual);

        $q->where('code', '=', 123);

        $actual = $q->getSql($params);
        $keys = array_keys($params);
        $expected = "UPDATE `user` SET `name` = :{$keys[0]}, `code` = :{$keys[1]}, `active` = :$keys[2] WHERE `code` = :{$keys[3]}";

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
}