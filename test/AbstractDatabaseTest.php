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
use c00\sample\DatabaseWithTrait;
use c00\sample\Team;

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

    public function testConnectWrongPassword(){
        $this->expectException(\PDOException::class);

        $host = "localhost";
        $user = "root";
        $pass = "Nothtepassword";
        $dbName = "test_common";

        //Abstract Database instance
        $db = new DatabaseWithTrait();
        $db->connect($host, $user, $pass, $dbName);
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

    }

    public function testDelete(){
        $q = Qry::delete(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44');

        $this->db->deleteRows($q);
    }

    public function testWrongDelete(){
        $this->expectException(QueryBuilderException::class);

        $q = Qry::select()
            ->from(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44');

        $this->db->deleteRows($q);
    }

}