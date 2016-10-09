<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 17/06/2016
 * Time: 11:03
 */

use c00\QueryBuilder\Query;
use c00\QueryBuilder\QueryBuilderException;

class QueryTest extends PHPUnit_Framework_TestCase
{
    public function testSelect(){
        $expected = "SELECT * FROM `user`";

        $query = new Query();
        $query->select()->from('user');

        $this->assertSame($expected, $query->getSql());

    }

    public function testSelectEncapped(){
        $expected = "SELECT * FROM `table`.`user`";

        $query = new Query();
        $query->select()->from('table.user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testOrderBy(){
        $expected = "SELECT * FROM `user` ORDER BY `user`.`name` ASC";

        $query = new Query();
        $query->select()->from('user')
            ->orderBy('user.name');

        $this->assertSame($expected, $query->getSql());

    }

    public function testOrderBy2(){

        $query = new Query();
        $query->select()->from('user')
            ->where('role', '=', 'admin')
            ->orderBy('user.name', false)
            ->limit(15);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` WHERE `role` = :{$key} ORDER BY `user`.`name` DESC LIMIT 15";

        $this->assertSame($expected, $sql);

    }

    public function testNewSelect(){
        $expected = "SELECT * FROM `user`";

        $query = Query::newSelect('user');

        $this->assertSame($expected, $query->getSql());

    }

    public function testSelectMax(){
        $expected = "SELECT MAX(`id`), `email` FROM `user`";

        $query = new Query();
        $query->select('email')
            ->max('id')
            ->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectMax2(){
        $expected = "SELECT MAX(`id`) FROM `user`";

        $query = new Query();
        $query->max('id')
            ->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectMax3(){
        $expected = "SELECT MAX(`created`), `challengeId`, `code`, `image`, `created`, `correct` FROM `answer`";

        $query = new Query();
        $query->select(['challengeId', 'code', 'image', 'created', 'correct' ])
            ->max('created')
            ->from('answer');

        $this->assertSame($expected, $query->getSql());
    }

    public function testNewSelectWhere(){
        $query = Query::newSelect('user', ['id' => 1]);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testNewSelectWhere2(){
        $query = Query::newSelect('user', ['id' => 1, 'email' => 'blaat@aap.com']);

        $params = [];
        $sql = $query->getSql($params);
        $id = array_keys($params)[0];
        $email = array_keys($params)[1];

        $expected = "SELECT * FROM `user` WHERE `id` = :{$id} AND `email` = :{$email}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhere(){
        $query = new Query();
        $query->select()->from('user')->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereIn(){
        $query = new Query();
        $query->select()->from('user')->whereIn('id', [1, 6, 8]);

        $params = [];
        $sql = $query->getSql($params);
        $keys = array_keys($params);

        $expected = "SELECT * FROM `user` WHERE `id` IN (:{$keys[0]}, :{$keys[1]}, :{$keys[2]})";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereIn2(){
        $query = new Query();
        $query->select()
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
        $query = new Query();
        $query->select()->from('user')->whereIn('id', []);

        $params = [];
        $sql = $query->getSql($params);

        $expected = "SELECT * FROM `user`";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhereIn4(){
        $query = new Query();
        $query->select()
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
        $query = new Query();
        $query->select([], true)->from('user')->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT DISTINCT * FROM `user` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectDistinctWhere2(){
        $query = new Query();
        $query->select(['id', 'name'], true)->from('user')->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT DISTINCT `id`, `name` FROM `user` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectWhere2(){
        $params = [];
        $query = new Query();
        $sql = $query->select()
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
        $query = new Query();
        $sql = $query->select(['id', 'user.email', 'user.*'])
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
        $query = new Query();
        $sql = $query->select(['id', 'emailAddress' => 'user.email'])
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
        $query = new Query();
        $sql = $query->select(['id', 'emailAddress' => 'user.email'])
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
        $query = new Query();
        $sql = $query->select()
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
        $query = new Query();
        $sql = $query->select()
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
        $qb = new Query();
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
        $q = new Query();
        $t = new \c00\sample\Team();
        $t->active = 1;
        $t->code = "teamcode";
        $t->name = "teamname";

        $q->update('user', $t);
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
        $q = new Query();
        $t = new \c00\sample\Team();
        $t->active = 1;
        $t->code = "teamcode";
        $t->name = "teamname";

        $q->insert('user', $t);
        $actual = $q->getSql($params);
        $keys = array_keys($params);

        $expected = "INSERT INTO `user` (`name`, `code`, `active`) VALUES(:$keys[0], :$keys[1], :$keys[2])";
        $this->assertEquals($expected, $actual);
    }

    public function testInsertArray(){
        $params = [];
        $q = new Query();
        $a = [
            'name' => 'karel',
            'code' => '123',
            'active' => 1
        ];

        $q->insert('user', $a);
        $actual = $q->getSql($params);
        $keys = array_keys($params);

        $expected = "INSERT INTO `user` (`name`, `code`, `active`) VALUES(:$keys[0], :$keys[1], :$keys[2])";
        $this->assertEquals($expected, $actual);
    }

    public function testDelete(){
        $expected = "DELETE FROM `user`";

        $query = new Query();
        $query->delete()->from('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testDelete2(){
        $expected = "DELETE FROM `user`";

        $query = new Query();
        $query->delete('user');

        $this->assertSame($expected, $query->getSql());
    }

    public function testDeleteWhere(){
        $query = new Query();
        $query->delete()
            ->from('user')
            ->where('id', '=', 1);

        $params = [];
        $sql = $query->getSql($params);
        $key = array_keys($params)[0];

        $expected = "DELETE FROM `user` WHERE `id` = :{$key}";

        $this->assertSame($expected, $sql);
    }

    public function testSelectJoin(){
        $query = new Query();
        $query->select()
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
        $query = new Query();
        $query->select()
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
        $query = new Query();
        $query->select()
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
        $query = new Query();
        $query->select()
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
        $query = new Query();
        $query->select()
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
        $query = new Query();
        $query->selectFunction("avg", "cost")
            ->from("product");

        $expected = "SELECT avg(`cost`) FROM `product`";

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectFunctions2(){
        $query = new Query();
        $query->select("user")
            ->selectFunction("avg", "cost")
            ->selectFunction("max", "age")
            ->from("product");

        //Note, this is hardly valid without a GROUP BY...
        $expected = "SELECT avg(`cost`), max(`age`), `user` FROM `product`";

        $this->assertSame($expected, $query->getSql());
    }

    public function testSelectFunctionsWithAlias(){
        $query = new Query();
        $query->select("user")
            ->selectFunction("avg", "cost", "your face")
            ->from("product");

        //Note, this is hardly valid without a GROUP BY...
        $expected = "SELECT avg(`cost`) as `your face`, `user` FROM `product`";

        $this->assertSame($expected, $query->getSql());
    }

    public function testNullIsNot0(){
        $q = new Query();
        $q->select()
            ->from('user')
            ->where('status', '=', 0);

        $params = [];
        $sql = $q->getSql($params);
        $key = array_keys($params)[0];

        $expected = "SELECT * FROM `user` WHERE `status` = :{$key}";
        $this->assertEquals($expected, $sql);
    }
}