<?php

namespace c00\common;

use c00\QueryBuilder\DebugInfo;
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
    private $dbName;

    private $hasOpenTransaction = false;
    public $debug = false;
    public $stats = [
        Qry::TYPE_INSERT => 0,
        Qry::TYPE_SELECT => 0,
        Qry::TYPE_UPDATE => 0,
        Qry::TYPE_DELETE => 0,
        'other' => 0,
        'transactions' => 0
    ];

    /** @var DebugInfo[] */
    public $qryInfo = [];
    /** @var  DebugInfo */
    private $currentDebugInfo;

    const NO_RECORD_FOUND = 1000;

    public function __construct()
    {

    }

    /**
     * @return DebugInfo|null
     */
    public function getLastQryInfo(){
        $count = count($this->qryInfo);
        if ($count === 0) return null;

        return $this->qryInfo[$count-1];
    }

    protected function connect($host, $user, $pass, $dbName, $port = null)
    {
        //Already connected? Just return true.
        if ($this->connected) return true;

        $dsn = "mysql:charset=utf8mb4;host=$host;dbname=$dbName";
        if ($port) $dsn .= ";port=$port";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        ];

        // Setup DB connection
        $this->db = new PDO($dsn, $user, $pass, $options);
        $this->dbName = $dbName;

        $this->connected = true;
        return true;
    }

    private function logQueryStart($type){
        $this->stats[$type]++;

        if (!$this->debug) return;

        $this->currentDebugInfo = DebugInfo::start();
    }

    private function logQueryEnd($q){
        if (!$this->debug || !$this->currentDebugInfo || get_class($q) != Qry::class) return;

        $this->currentDebugInfo->finish($q);
        $this->qryInfo[] = $this->currentDebugInfo;
        $this->currentDebugInfo = null;
    }

    public function beginTransaction(){
        $this->hasOpenTransaction = true;
        $this->stats['transactions']++;
        $this->db->beginTransaction();
    }

    public function commitTransaction(){
        $this->hasOpenTransaction = false;
        $this->db->commit();
    }

    public function rollBackTransaction(){
        $this->hasOpenTransaction = false;
        $this->db->rollBack();
    }

    public function getColumns($table){
        $table = trim($table, '`');

        $q = $this->db->prepare("DESCRIBE `$table`");
        $q->execute();

        $this->stats['other']++;

        return $q->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Gets the value of the first column of the first row.
     *
     * @param Qry $q The query to execute
     * @return mixed
     * @throws \Exception When there's no result
     */
    public function getValue(Qry $q){
        $row = $this->getRow($q);
        foreach ($row as $item) {
            return $item;
        }

        throw new \Exception("No value to return");
    }

    /**
     * Gets the values of the first column of each row.
     *
     * @param Qry $q The query to execute
     * @return array
     */
    public function getValues(Qry $q){
        $rows = $this->getRows($q);

        $values = [];

        foreach ($rows as $row) {

            foreach ($row as $value) {
                $values[] = $value;
                break;
            }
        }

        return $values;
    }

    public function hasTable($table){
        $q = Qry::select('table_name')
            ->from('information_schema.tables')
            ->where('table_schema', '=', $this->dbName)
            ->where('table_name', '=', $table);

        return $this->rowExists($q);
    }

    public function hasTables($tables){
        $q = Qry::select()
            ->count('table_name')
            ->from('information_schema.tables')
            ->where('table_schema', '=', $this->dbName)
            ->whereIn('table_name', $tables);

        //Return true if the number returned is the same as the number of tables.
        return ($this->getValue($q) == count($tables));
    }

    public function hasColumn($table, $column){
        $columns = $this->getColumns($table);

        return in_array($column , $columns);
    }

    public function updateRow(IQry $q, $returnRowCount = false)
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

        $this->logQueryStart(Qry::TYPE_UPDATE);
        $result = $statement->execute();
        $this->logQueryEnd($q);

        if ($returnRowCount){
            return $statement->rowCount();
        } else {
            return $result;
        }
    }

    /**
     * @param IQry $q
     * @param bool $noId Set this to true if the table does not use an ID column.
     * @return bool|string|int True or false to indicate success. Or the ID of the new row when $noId = false.
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

        $this->logQueryStart($q->getType());
        if (!$statement->execute()) {
            throw new \Exception("Couldn't insert row.");
        }
        $this->logQueryEnd($q);

        if ($noId) return true;
        $id = $this->db->lastInsertId();
        if (is_numeric($id)) return (int) $id;

        return $id;
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

    /**
     * @param IQry $q The query
     * @param bool $toShowable Convert result to Showable
     * @return array
     * @throws QueryBuilderException
     * @throws \Exception
     */
    public function getRows($q, $toShowable = false)
    {
        $records = $this->getRecords($q);
        //Return a list of Object?
        $className = $q->getClass();

        //Return if we don't need to make objects
        if ($className === '') return $records;

        //Check interface implement
        if (!is_subclass_of($className, IDatabaseObject::class)) {
            throw new \Exception("Class $className doesn't implement IDatabaseObject");
        }

        $result = [];
        foreach ($records as $record) {
            if ($toShowable) {
                $result[] = $className::fromArray($record)->toShowable();
            } else {
                $result[] = $className::fromArray($record);
            }
        }

        return $result;
    }

    /** Used to get database records turned into a collection of objects.
     *
     * The result will be an associative array with the table alias as key, and the array of objects as as the value.
     * The array of objects is also associative where the keys are the object ids.
     *
     * @param Qry $q The query
     * @param bool $toShowable Convert result to Showable
     * @return array the resulting objects
     * @throws QueryBuilderException
     * @throws \Exception
     */
    public function getObjects($q, $toShowable = false)
    {
        $records = $this->getRecords($q);

        $builder = ObjectBuilder::newInstance($q)->build($records);

        $objects = $builder->objects;

        if (!$toShowable) return $objects;

        foreach ($objects as &$collection) {

            foreach ($collection as &$object) {
                /** @var IDatabaseObject $object */
                $object = $object->toShowable();
            }
        }

        return $objects;
    }

    /**
     * @param $q IQry
     * @return array
     * @throws QueryBuilderException
     */
    private function getRecords($q) {
        if ($q->getType() != Qry::TYPE_SELECT){
            throw new QueryBuilderException("Wrong Query type!");
        }

        $statement = $this->db->prepare($q->getSql());
        $where = $q->getParams();

        if (!$statement) throw new QueryBuilderException("Invalid Query statement.");

        $this->bindWhereClause($statement, $where);

        $this->logQueryStart($q->getType());
        $statement->execute();
        $this->logQueryEnd($q);

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $records;
    }

    public function deleteRows(IQry $q, $returnRowCount = false)
    {
        if ($q->getType() != Qry::TYPE_DELETE){
            throw new QueryBuilderException("Wrong Query type!");
        }

        $statement = $this->db->prepare($q->getSql());
        $where = $q->getParams();

        if (!$statement) return false;

        $this->bindWhereClause($statement, $where);

        $this->logQueryStart($q->getType());
        $result = $statement->execute();
        $this->logQueryEnd($q);

        if ($returnRowCount){
            return $statement->rowCount();
        } else {
            return $result;
        }
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