<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 09/10/2016
 * Time: 21:43
 */

namespace test;

use c00\QueryBuilder\Qry;
use c00\QueryBuilder\QueryBuilderException;
use c00\QueryBuilder\Ranges;
use c00\sample\DatabaseWithTrait;
use c00\sample\Team;
use Prophecy\Exception\Exception;

class AbstractDatabaseTest extends \PHPUnit_Framework_TestCase
{
    const TABLE_TEAM = 'team';

    /** @var DatabaseWithTrait */
    private $db;
    /** @var \PDO */
    private $pdo;

    public function setUp(){
        $host = "localhost";
        $user = "root";
        $pass = "";
        $dbName = "test_common";

        //Abstract Database instance
        $this->db = new DatabaseWithTrait();
        $this->db->connect($host, $user, $pass, $dbName);

        //PDO instance
        $this->pdo = new \PDO(
            "mysql:charset=utf8mb4;host=$host;dbname=$dbName",
            $user,
            $pass,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_EMULATE_PREPARES => false]
        );

        //Run fixture. This removes all content in the database and resets to the primary set.
        $sql = file_get_contents(__DIR__ . '/sql/fixture.sql');
        $this->pdo->exec($sql);
    }

    public function testCompressedConnection(){
        $host = "localhost";
        $user = "root";
        $pass = "";
        $dbName = "test_common";

        $db = new DatabaseWithTrait();
        $db->connect($host, $user, $pass, $dbName, null, true);

        //$pdo = $db->getDb();

        //$driver = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        //$this->assertTrue($pdo->getAttribute(\PDO::MYSQL_ATTR_COMPRESS));


    }

    public function testConnectWrongPassword(){
        $this->expectException(\PDOException::class);

        $host = "localhost";
        $user = "root";
        $pass = "Notthepassword";
        $dbName = "test_common";

        //Abstract Database instance
        $db = new DatabaseWithTrait();
        $db->connect($host, $user, $pass, $dbName);
    }

    public function testGetColumns(){
        $columns = $this->db->getColumns(self::TABLE_TEAM);
        $this->assertEquals(5, count($columns));
    }

    public function testHasColumn(){
        $this->assertTrue($this->db->hasColumn(self::TABLE_TEAM, 'id'));
        $this->assertTrue($this->db->hasColumn(self::TABLE_TEAM, 'code'));
        $this->assertTrue($this->db->hasColumn(self::TABLE_TEAM, 'name'));

        $this->assertFalse($this->db->hasColumn(self::TABLE_TEAM, 'ID'));
        $this->assertFalse($this->db->hasColumn(self::TABLE_TEAM, 'Your mom'));
    }

    public function testSelect1(){
        /** @var Team $team */
        $q = Qry::select()
            ->from(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44')
            ->asClass(Team::class);

        $team = $this->db->getRow($q);

        $this->assertTrue($team instanceof Team);
        $this->assertEquals('The Dudemeisters', $team->name);

        $this->assertEquals(1, $this->db->stats['select']);
    }

    public function testSelectAll(){
        /** @var Team $team */
        $q = Qry::select()
            ->from(self::TABLE_TEAM)
            ->asClass(Team::class);

        $teams = $this->db->getRows($q);

        $this->assertEquals(3, count($teams));

        /** @var Team $team2 */
        $team2 = $teams[1];
        $this->assertSame('The Chimpmunks', $team2->name);
        $this->assertSame(2, $team2->id);
        $this->assertNotSame("2", $team2->id);
        $this->assertSame('cattle6', $team2->code);
        $this->assertSame(true, $team2->active);
        $this->assertNotSame(1, $team2->active);
    }

    public function testInsert(){
        $team = new Team();
        $team->name = "Testers";
        $team->active = true;
        $team->code = "test123";

        $id = $this->db->insertRow(Qry::insert(self::TABLE_TEAM, $team));

        $this->assertEquals(4, $id);
    }

    public function testUpdate(){
        /** @var Team $team */
        $q = Qry::select()
            ->from(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44')
            ->asClass(Team::class);

        $team = $this->db->getRow($q);

        $team->name = "Supreme donkey of the trouser pods";

        $q2 = Qry::update(self::TABLE_TEAM, $team, ['id' => $team->id]);
        $this->assertTrue($this->db->updateRow($q2));

        $team2 = $this->db->getRow($q);
        $this->assertEquals($team->name, $team2->name);
        $this->assertEquals($team->name, "Supreme donkey of the trouser pods");


        $this->assertEquals(2, $this->db->stats['select']);
        $this->assertEquals(1, $this->db->stats['update']);

    }

    public function testUpdateReturnRowCount(){

        $newName = 'We are the borg';
        //Update all
        $q1 = Qry::update(self::TABLE_TEAM, ['name' => $newName])->where('id', '>', 0);

        $this->assertEquals(3, $this->db->updateRow($q1, true));

        $q = Qry::select()
            ->from(self::TABLE_TEAM)
            ->asClass(Team::class);

        /** @var Team[] $teams */
        $teams = $this->db->getRows($q);

        foreach ($teams as $team) {
            $this->assertEquals($newName, $team->name);
        }
    }

    public function testDelete(){
        $q = Qry::delete(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44');

        $this->db->deleteRows($q);
    }

    public function testDeleteReturnRowCount(){
        $q = Qry::delete(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44');

        $this->assertEquals(1, $this->db->deleteRows($q, true));

        $q2 = Qry::delete(self::TABLE_TEAM)
            ->where('id', '>', 0);

        $this->assertEquals(2, $this->db->deleteRows($q2, true));
    }

    public function testWrongDelete(){
        $this->expectException(QueryBuilderException::class);

        $q = Qry::select()
            ->from(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44');

        $this->db->deleteRows($q);
    }

    public function testRangesQuery(){
        $ranges =  Ranges::newRanges('created', 'period');

        $ranges->addCaseLessThan('1 early', 1473230176);
        $ranges->addCaseBetween('2 normal', 1473230176, 1473233802);
        $ranges->addCaseGreaterThan('3 late',1473233802);


        $q = Qry::selectRange($ranges)
            ->count('id', 'count')
            ->from('answer')
            ->orderBy('period');

        $params = [];
        $sql = $q->getSql($params);
        $keys = array_keys($params);

        //Test query
        $expected = "SELECT CASE WHEN `created` < :{$keys[0]} THEN '1 early' WHEN `created` BETWEEN :{$keys[1]} AND :{$keys[2]} THEN '2 normal' WHEN `created` > :{$keys[3]} THEN '3 late' END AS `period`, COUNT(`id`) AS `count` FROM `answer` GROUP BY `period` ORDER BY `period` ASC";
        $this->assertEquals($expected, $sql);

        //Execute on database
        $result = $this->db->getRows($q);

        $this->assertEquals(3, count($result));

        //Test row 1
        $this->assertEquals('1 early', $result[0]['period']);
        $this->assertEquals(7, $result[0]['count']);

        //Test row 2
        $this->assertEquals('2 normal', $result[1]['period']);
        $this->assertEquals(40, $result[1]['count']);

        //Test row 3
        $this->assertEquals('3 late', $result[2]['period']);
        $this->assertEquals(192, $result[2]['count']);

    }

    public function testHasTable(){
        $this->assertTrue($this->db->hasTable("user"));
        $this->assertFalse($this->db->hasTable("foo"));
    }

    public function testHasTables(){
        $goodTables = ['team', 'session', 'user'];
        $badTables1 = ['team', 'session', 'foo'];
        $badTables2 = ['team', 'session', 'user', 'foo'];
        $badTables3 = [];


        $this->assertTrue($this->db->hasTables($goodTables));
        $this->assertfalse($this->db->hasTables($badTables1));
        $this->assertfalse($this->db->hasTables($badTables2));
        $this->assertfalse($this->db->hasTables($badTables3));

    }

    public function testGetValue(){
        $q = Qry::select('name')
            ->from(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44');

        $value = $this->db->getValue($q);

        $this->assertEquals('The Dudemeisters', $value);
    }

    public function testGetValue2(){
        $q = Qry::select()
            ->count('id')
            ->from(self::TABLE_TEAM);

        $value = $this->db->getValue($q);

        $this->assertEquals(3, $value);
    }

    public function testGetValues(){
        $q = Qry::select('name')
            ->from(self::TABLE_TEAM);

        $values = $this->db->getValues($q);

        $expected = [
            'The Dudemeisters',
            'The Chimpmunks',
            'Crazy Horses'
        ];


        $this->assertEquals($expected, $values);
    }

    public function testTransactionCommit(){
        $this->db->beginTransaction();

        $team = new Team();
        $team->name = "Testers";
        $team->active = true;
        $team->code = "test123";

        $id = $this->db->insertRow(Qry::insert(self::TABLE_TEAM, $team));

        $this->assertEquals(4, $id);

        $teamBeforeCommit = $this->db->getRow(Qry::select()->from(self::TABLE_TEAM)->where('id', '=', $id)->asClass(Team::class));
        $this->assertEquals($team->name, $teamBeforeCommit->name);

        $this->db->commitTransaction();

        $this->assertEquals(1, $this->db->stats['transactions']);

        $teamFromDb = $this->db->getRow(Qry::select()->from(self::TABLE_TEAM)->where('id', '=', $id)->asClass(Team::class));
        $this->assertEquals($team->name, $teamFromDb->name);
    }

    public function testTransactionRollback(){
        $this->db->beginTransaction();

        $team = new Team();
        $team->name = "Testers";
        $team->active = true;
        $team->code = "test123";

        $id = $this->db->insertRow(Qry::insert(self::TABLE_TEAM, $team));

        $this->assertEquals(4, $id);

        $teamBeforeCommit = $this->db->getRow(Qry::select()->from(self::TABLE_TEAM)->where('id', '=', $id)->asClass(Team::class));
        $this->assertEquals($team->name, $teamBeforeCommit->name);

        $this->db->rollBackTransaction();

        $this->assertEquals(1, $this->db->stats['transactions']);

        $this->expectException(\Exception::class);
        $this->db->getRow(Qry::select()->from(self::TABLE_TEAM)->where('id', '=', $id)->asClass(Team::class));
    }


    public function testDebug1(){
        $this->db->debug = true;

        $q = Qry::delete(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44');
        $this->db->deleteRows($q);

        $q2 = Qry::delete(self::TABLE_TEAM)
            ->where('id', '>', 0);
        $this->db->deleteRows($q2);


        $qryInfo = $this->db->qryInfo;

        $this->assertEquals(2, count($qryInfo));

        $this->assertEquals("DELETE FROM `team` WHERE `code` = 'aapjes44'", $qryInfo[0]->sql);
        $this->assertEquals("DELETE FROM `team` WHERE `id` > 0", $qryInfo[1]->sql);
        $this->assertEquals(0, $this->db->stats[Qry::TYPE_SELECT]);
        $this->assertEquals(0, $this->db->stats[Qry::TYPE_INSERT]);
        $this->assertEquals(0, $this->db->stats[Qry::TYPE_UPDATE]);
        $this->assertEquals(2, $this->db->stats[Qry::TYPE_DELETE]);
        $this->assertEquals(0, $this->db->stats['other']);
        $this->assertEquals(0, $this->db->stats['transactions']);
    }

    public function testDebug2(){
        $q = Qry::delete(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44');
        $this->db->deleteRows($q);

        $q2 = Qry::delete(self::TABLE_TEAM)
            ->where('id', '>', 0);
        $this->db->deleteRows($q2);


        $qryInfo = $this->db->qryInfo;

        $this->assertEquals(0, count($qryInfo));
        $this->assertEquals(0, $this->db->stats[Qry::TYPE_SELECT]);
        $this->assertEquals(0, $this->db->stats[Qry::TYPE_INSERT]);
        $this->assertEquals(0, $this->db->stats[Qry::TYPE_UPDATE]);
        $this->assertEquals(2, $this->db->stats[Qry::TYPE_DELETE]);
        $this->assertEquals(0, $this->db->stats['other']);
        $this->assertEquals(0, $this->db->stats['transactions']);
    }
}