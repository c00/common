<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 09/10/2016
 * Time: 21:43
 */

namespace test;

use c00\common\DatabaseException;
use c00\QueryBuilder\Qry;
use c00\QueryBuilder\QueryBuilderException;
use c00\QueryBuilder\Ranges;
use c00\sample\DatabaseWithTrait;
use c00\sample\Team;
use c00\sample\TeamSession;

class AbstractDatabaseTest extends \PHPUnit_Framework_TestCase
{
    const TABLE_TEAM = 'team';
    const TABLE_TEAM_SESSION = 'teamsession';

    /** @var DatabaseWithTrait */
    private $db;
    /** @var \PDO */
    private $pdo;

    public function setUp(){
        $host = "127.0.0.1";
        $user = "root";
        $pass = "password";
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
        $host = "127.0.0.1";
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

        $host = "127.0.0.1";
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

	public function testGroupConcatSeparator(){
		$q = Qry::select()
				->groupConcat('name', 'name', null, ' | ')
		        ->from(self::TABLE_TEAM);

		$actual = $this->db->getValue($q);

		$expected = "The Dudemeisters | The Chimpmunks | Crazy Horses";

		$this->assertEquals($expected, $actual);
	}

    public function testSelectImage(){
        /** @var Team $team */
        $q = Qry::select()
            ->from(self::TABLE_TEAM)
            ->orderByNull('image', false)
            ->orderBy('name')
            ->asClass(Team::class);

        /** @var Team[] $teams */
        $teams = $this->db->getRows($q);

        $this->assertEquals('The Chimpmunks', $teams[0]->name);
        $this->assertNull($teams[0]->image);

        $this->assertEquals('Crazy Horses', $teams[1]->name);
        $this->assertNotNull($teams[1]->image);

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

        $this->assertTrue(is_int($id));
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

    public function testUpdateWithJoin(){

        $q = Qry::update(['t' => self::TABLE_TEAM], ['t.name' => 'Supreme donkey of the trouser pods'])
            ->join(['ts' => self::TABLE_TEAM_SESSION], 't.id', '=', 'ts.teamId')
            ->where('ts.token', '=', '5bf1fd927dfb8679496a2e6cf00cbe50c1c87145');

        $this->assertTrue($this->db->updateRow($q));

        $team = $this->db->getRow(
            Qry::select('t.*')
            ->from(['t' => self::TABLE_TEAM])
                ->join(['ts' => self::TABLE_TEAM_SESSION], 't.id', '=', 'ts.teamId')
                ->where('ts.token', '=', '5bf1fd927dfb8679496a2e6cf00cbe50c1c87145')
                ->asClass(Team::class)
        );

        $this->assertEquals($team->name, "Supreme donkey of the trouser pods");

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

    public function testGetQryInfo(){
        $this->assertNull($this->db->getLastQryInfo());

        $this->db->debug = true;
        $q = Qry::delete(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44');
        $this->db->deleteRows($q);

        $actual = $this->db->getLastQryInfo()->sql;
        $expected = "DELETE FROM `team` WHERE `code` = 'aapjes44'";


        $this->assertEquals($actual, $expected);


    }

    public function testGetObjects1() {
        $q = Qry::select()
            ->fromClass(Team::class, 'team', 't');

        $objects = $this->db->getObjects($q);

        $this->assertEquals(3, count($objects['t']));

        $team = $objects['t'][1];

        $this->assertTrue($team instanceof Team);
        $this->assertEquals('The Dudemeisters', $team->name);
    }

    public function testGetObjects2() {
        $q = Qry::select()
            ->fromClass(Team::class, 'team', 't');

        $objects = $this->db->getObjects($q, true);

        $this->assertEquals(3, count($objects['t']));

        $team = $objects['t'][1];

        $this->assertTrue(is_array($team));
        $this->assertEquals('The Dudemeisters', $team['name']);
    }

    public function testGetObjects3() {
        $q = Qry::select()
            ->fromClass(Team::class, 'team', 't')
            ->joinClass(TeamSession::class, 'teamsession', 'ts', 't.id', '=', 'ts.teamId');

        $objects = $this->db->getObjects($q);

        $this->assertEquals(2, count($objects['t']));
        $this->assertEquals(2, count($objects['ts']));

        $token = '5bf1fd927dfb8679496a2e6cf00cbe50c1c87145';
        $session = $objects['ts'][$token];

        $this->assertTrue($session instanceof TeamSession);
    }

    public function testGetObjects4() {
        $q = Qry::select()
            ->fromClass(Team::class, 'team', 't')
            ->outerJoinClass(TeamSession::class, 'teamsession', 'ts', 't.id', '=', 'ts.teamId');

        $objects = $this->db->getObjects($q);

        $this->assertEquals(3, count($objects['t']));
        $this->assertEquals(2, count($objects['ts']));

        $token = '5bf1fd927dfb8679496a2e6cf00cbe50c1c87145';
        $session = $objects['ts'][$token];

        $this->assertTrue($session instanceof TeamSession);
    }

	public function testGetObjects5() {
		$q = Qry::select()
		        ->fromClass(Team::class, 'team', 't')
		        ->outerJoinClass(TeamSession::class, 'teamsession', 'ts', 't.id', '=', 'ts.teamId')
		        ->where('t.id', '=', 1);

		$objects = $this->db->getObjects($q);

		$this->assertEquals(1, count($objects['t']));
		$this->assertEquals(1, count($objects['ts']));

		$token = '5bf1fd927dfb8679496a2e6cf00cbe50c1c87145';
		$session = $objects['ts'][$token];

		$this->assertTrue($session instanceof TeamSession);
	}

	public function testGetObjects6() {
		$q = Qry::select(['ts.*', 't.id' => 't.id', 't.name' => 't.name', 't.code' => 't.code'])
		        ->fromClass(TeamSession::class, 'teamsession', 'ts')
		        ->join(['t' => 'team'], 't.id', '=', 'ts.teamId')
		        ->where('t.id', '=', 1);

		$objects = $this->db->getObjects($q);

		$this->assertEquals(1, count($objects['t']));
		$this->assertEquals(1, count($objects['ts']));



		$token = '5bf1fd927dfb8679496a2e6cf00cbe50c1c87145';
		$session = $objects['ts'][$token];

		$team = $objects['t'][1];
		$this->assertTrue(is_array($team));
		$this->assertEquals($team['name'], 'The Dudemeisters');

		$this->assertTrue($session instanceof TeamSession);
	}

	public function testTransactions1() {
        $this->assertFalse($this->db->hasOpenTransaction());
        $this->db->beginTransaction();

        $this->assertTrue($this->db->hasOpenTransaction());

        $this->expectException(DatabaseException::class);
        $this->db->beginTransaction();
    }

    public function testTransactions2() {
        $this->db->allowNestedTransactions = true;
        $this->assertFalse($this->db->hasOpenTransaction());

        $this->db->beginTransaction();
        $this->assertTrue($this->db->hasOpenTransaction());

        //Open second one.
        $this->db->beginTransaction();
        $this->assertTrue($this->db->hasOpenTransaction());

        //Open third one.
        $this->db->beginTransaction();
        $this->assertTrue($this->db->hasOpenTransaction());

        //Close third
        $this->db->commitTransaction();
        $this->assertTrue($this->db->hasOpenTransaction());

        //Close second
        $this->db->commitTransaction();
        $this->assertTrue($this->db->hasOpenTransaction());

        //Close first
        $this->db->commitTransaction();
        $this->assertfalse($this->db->hasOpenTransaction());
    }

    public function testTransactions3() {
        $this->db->allowNestedTransactions = true;

        $this->assertFalse($this->db->hasOpenTransaction());

        //Open 3
        $this->db->beginTransaction();
        $this->db->beginTransaction();
        $this->db->beginTransaction();

        $this->assertTrue($this->db->hasOpenTransaction());

        //rollback just one
        $this->db->rollBackTransaction();
        $this->assertFalse($this->db->hasOpenTransaction());
    }

}