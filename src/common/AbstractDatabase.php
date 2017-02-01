<?php

namespace c00\common;

use c00\QueryBuilder\Qry;
use c00\QueryBuilder\QueryBuilderException;
use \PDO;
use c00\QueryBuilder\IQry;

/**
 * Class AbstractDatabase
 * This is an abstract class that implements standard PDO actions.
 */
abstract class AbstractDatabase
{
    /** @var PDO */
    protected $db;
    protected $connected;

    const NO_RECORD_FOUND = 1000;

    public function __construct()
    {

    }

    protected function connect($host, $user, $pass, $dbName)
    {
        //Already connected? Just return true.
        if ($this->connected) return true;

        // Setup DB connection
        $this->db = new PDO(
            "mysql:charset=utf8mb4;host=$host;dbname=$dbName",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
        );

        $this->connected = true;
        return true;
    }

    public function getColumns($table){
        $table = trim($table, '`');

        $q = $this->db->prepare("DESCRIBE `$table`");
        $q->execute();

        return $q->fetchAll(PDO::FETCH_COLUMN);
    }

    public function hasColumn($table, $column){
        $columns = $this->getColumns($table);

        return in_array($column , $columns);
    }

    public function updateRow(IQry $q)
    {
        if ($q->getType() != Qry::TYPE_UPDATE){
            throw new QueryBuilderException("Wrong Query type!");
        }

        //protect against inadvertently updating everything
        if ($q->whereCount() == 0) {
            throw new QueryBuilderException("No WHERE clause!");
        }

        $sql = $q->getSql();
        $params = $q->getParams();

        $statement = $this->db->prepare($sql);
        $this->bindValues($statement, $params);

        return $statement->execute();
    }

    /**
     * @param IQry $q
     * @param bool $noId Set this to true if the table does not use an ID column.
     * @return bool|string True or false to indicate success. Or the ID of the new row when $noId = false.
     * @throws \Exception
     */
    public function insertRow(IQry $q, $noId = false)
    {
        if ($q->getType() != Qry::TYPE_INSERT){
            throw new QueryBuilderException("Wrong Query type!");
        }

        $statement = $this->db->prepare($q->getSql());
        $params = $q->getParams();
        $this->bindValues($statement, $params);

        if (!$statement->execute()) {
            throw new \Exception("Couldn't insert row.");
        }

        return ($noId) ? true : $this->db->lastInsertId();
    }

    public function getRow(IQry $q, $toShowable = false)
    {
        if ($q->getType() != Qry::TYPE_SELECT){
            throw new QueryBuilderException("Wrong Query type!");
        }

        $q->limit(1);
        $result = $this->getRows($q, $toShowable);
        if (!isset($result[0])) {
            throw new \Exception("No record found", self::NO_RECORD_FOUND);
        }
        return $result[0];
    }
    
    public function getRows(IQry $q, $toShowable = false)
    {
        if ($q->getType() != Qry::TYPE_SELECT){
            throw new QueryBuilderException("Wrong Query type!");
        }

        $statement = $this->db->prepare($q->getSql());
        $where = $q->getParams();

        if (!$statement) return false;

        $this->bindWhereClause($statement, $where);

        $statement->execute();

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        //Return a list of Object?
        $className = $q->getClass();
        if (empty($className)) return $records;

        //Check interface implement
        $object = new $className();
        if (!$object instanceof IDatabaseObject) {
            throw new \Exception("This class doesn't implement IDatabaseObject");
        }

        $result = [];
        foreach ($records as $record) {
            if ($toShowable) {
                $result[] = $object::fromArray($record)->toShowable();
            } else {
                $result[] = $object::fromArray($record);
            }
        }

        return $result;
    }

    public function deleteRows(IQry $q)
    {
        if ($q->getType() != Qry::TYPE_DELETE){
            throw new QueryBuilderException("Wrong Query type!");
        }

        $statement = $this->db->prepare($q->getSql());
        $where = $q->getParams();

        if (!$statement) return false;

        $this->bindWhereClause($statement, $where);

        return $statement->execute();
    }

    /**
     * @param $q IQry
     * @return bool
     */
    public function rowExists(IQry $q)
    {
        $q->limit(1);
        $result = $this->getRows($q);

        return (count($result) > 0);
    }

    private function bindValues(\PDOStatement &$statement, $values){
        foreach ($values as $key => $value) {
            if ($value === null) {
                $statement->bindValue(":$key", null, PDO::PARAM_INT);
            } else if (is_array($value) || is_object($value)) {
                $statement->bindValue(":$key", json_encode($value));
            } else {
                $statement->bindValue(":$key", $value);
            }
        }
    }

    private function bindWhereClause(\PDOStatement &$statement, $where){
        if (is_numeric($where)) $where = ['id' => $where];
        
       $this->bindValues($statement, $where);
    }
}