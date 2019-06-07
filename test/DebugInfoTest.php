<?php

namespace test;

use c00\QueryBuilder\DebugInfo;
use c00\QueryBuilder\Qry;
use c00\sample\DatabaseWithTrait;
use c00\sample\Team;
use PHPUnit\Framework\TestCase;

class DebugInfoTest extends TestCase
{
    const TABLE_TEAM = 'team';

    /** @var DatabaseWithTrait */
    private $db;
    /** @var \PDO */
    private $pdo;

    public function setUp(): void {
        $host = "127.0.0.1";
        $user = "root";
        $pass = "root";
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

    public function testBasic(){
        /** @var Team $team */
        $q = Qry::select()
            ->from(self::TABLE_TEAM)
            ->where('code', '=', 'aapjes44')
            ->asClass(Team::class);

        $this->db->getRow($q);

        $info = DebugInfo::start();
        sleep(1);
        $info->finish($q);

        //I'm suggesting the start and finish will be called within 1 second and 100 microseconds of each other.
        $this->assertTrue($info->getDifference() > 1);
        $this->assertTrue($info->getDifference() < 1.1);

        $this->assertEquals("SELECT * FROM `team` WHERE `code` = 'aapjes44' LIMIT 1", $info->sql);
    }

    public function testTransaction(){
        $d = new DebugInfo();
        $d->start();
        $d->finish(DebugInfo::TYPE_TRANSACTION);

        $this->assertEquals('transaction', $d->sql);
    }
}