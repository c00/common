<?php

namespace c00\common;

use c00\QueryBuilder\Query;
use \PDO;
use \PDOException;
use c00\log\Log;

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
    public function updateRow(Query $q)
    {
        //protect against inadvertently updating everything
        if ($q->whereCount() == 0) {
            throw new \Exception("No WHERE clause!");
        }

        $sql = $q->getSql();
        $params = array_merge($q->getWhereParams(), $q->getUpdateParams());

        $statement = $this->db->prepare($sql);
        $this->bindValues($statement, $params);

        return $statement->execute();
    }

    /**
     * @param Query $q
     * @param bool $noId Set this to true if the table does not use an ID column.
     * @return bool|string True or false to indicate success. Or the ID of the new row when $noId = false.
     * @throws \Exception
     */
    public function insertRow(Query $q, $noId = false)
    {
        $statement = $this->db->prepare($q->getSql());
        $params = $q->getInsertParams();
        $this->bindValues($statement, $params);

        if (!$statement->execute()) {
            throw new \Exception("Couldn't insert row.");
        }

        return ($noId) ? true : $this->db->lastInsertId();
    }

    public function getRow(Query $q, $toShowable = false)
    {
        $q->limit(1);
        $result = $this->getRows($q, $toShowable);
        if (!isset($result[0])) {
            Log::error("No record");
            Log::debug("Query", $q);
            throw new \Exception("No record found", self::NO_RECORD_FOUND);
        }
        return $result[0];
    }
    
    public function getRows(Query $q, $toShowable = false)
    {
        $statement = $this->db->prepare($q->getSql());
        $where = $q->getWhereParams();

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

    public function deleteRows(Query $q)
    {
        $statement = $this->db->prepare($q->getSql());
        $where = $q->getWhereParams();

        if (!$statement) return false;

        $this->bindWhereClause($statement, $where);

        return $statement->execute();
    }

    protected function connect($host, $user, $pass, $dbName)
    {
        //Already connected? Just return true.
        if ($this->connected) return true;

        // Setup DB connection
        try {
            $this->db = new PDO(
                "mysql:charset=utf8mb4;host=$host;dbname=$dbName",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
            );
        } catch (PDOException $e) {
            Log::error("Error connecting to database: {$e->getMessage()}");
            Log::debug("Code: " . $e->getCode() . " Message: " . $e->getMessage());
            //todo: Make JSON response

            http_response_code(500);
            die("<h1>Can't connect to database</h1>");
        }

        $this->connected = true;
        return true;
    }

    /** Gets rows from the database
     * @param string $table The name of the table.
     * @param array $columns Which columns to include (default will return all)
     * @param array $where The WHERE clause [columns => value]
     * @param string $whereAddon The custom addon to the WHERE clause.
     * @param array|string $order 'Order by' array ['column1', 'column2 DESC'], or string 'column1'
     * @return array|bool Assoc Array with the results, or false on error.
     * @deprecated use getRows instead
     */
    protected function getRecords($table, $columns = [], $where = [], $whereAddon = null, $order = [])
    {
        $columnString = (count($columns) == 0) ? "*" : join(', ', $columns);
        $whereString = $this->getWhereClause($where);
        $orderString = $this->getOrderString($order);

        $sql = "SELECT {$columnString} FROM `{$table}` {$whereString} {$orderString}";

        if ($whereAddon !== null) $sql .= " AND $whereAddon";

        $statement = $this->db->prepare($sql);

        if (!$statement) return false;

        $this->bindWhereClause($statement, $where);

        $statement->execute();

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $records;
    }

    /** Gets one row (record) from the database
     * @param string $table The table name
     * @param array $columns The columns to return (defaults to all)
     * @param array $where The where clause
     * @param string $whereAddon Optional SQL text that will be pasted at the end of the where clause.
     * @return bool|array Returns the row as an assoc array on success. False otherwise.
     * @deprecated use getRow instead
     */
    protected function getRecord($table, $columns = [], $where = [], $whereAddon = null)
    {
        $columnString = (count($columns) == 0) ? "*" : join(', ', $columns);
        $whereString = $this->getWhereClause($where);

        $sql = "SELECT {$columnString} FROM `{$table}` {$whereString}";
        if ($whereAddon !== null) $sql .= " AND $whereAddon";

        $statement = $this->db->prepare($sql);

        $this->bindWhereClause($statement, $where);

        $statement->execute();

        $record = $statement->fetch(PDO::FETCH_ASSOC);

        if ($record === false) return false;

        return $record;
    }

    /**
     * @param $q Query
     * @return bool
     */
    public function rowExists(Query $q)
    {
        $q->limit(1);
        $result = $this->getRows($q);

        return (count($result) > 0);
    }

    /**
     * @param $table
     * @param $column
     * @param $value
     * @return bool
     * @deprecated use rowExists instead
     */
    public function recordExists($table, $column, $value)
    {
        $sql = "SELECT $column FROM `$table` WHERE $column = :value";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':value', $value);
        $statement->execute();

        if ($statement->rowCount() < 1) return false;

        return true;
    }

    /** Execute an Insert Statement
     * @param $table string The Table name
     * @param $data array|IDatabaseObject An Assoc Array of columns and values, or an IDatabaseObject.
     * @param bool $ignoreId Indicates if we should remove the ID from the $data array. Defaults to true.
     * @param bool $replace Make a REPLACE statement rather then INSERT
     * @return bool|int The ID on Success, false otherwise.
     */
    protected function insert($table, $data, $ignoreId = true, $replace = false)
    {
        if (is_object($data) && !($data instanceof IDatabaseObject)) return false;
        if (is_object($data)) $data = $data->toArray();

        if ($ignoreId) unset($data['id']);

        $columns = '`' . join('`, `', array_keys($data)) . '`';
        $values = ':' . join(', :', array_keys($data));

        $keyword = ($replace) ? "REPLACE" : "INSERT";

        $sql = "$keyword INTO `$table` ({$columns}) VALUES({$values})";

        //split functions from values.
        $values = [];
        foreach ($data as $key => $value) {
            if ($value === '%NOW%') {
                $sql = str_replace(":$key", 'NOW()', $sql);
            } elseif ($value === '%TIMESTAMP%') {
                $sql = str_replace(":$key", 'UNIX_TIMESTAMP()', $sql);
            } else {
                $values[$key] = $value;
            }
        }

        //Prepare
        $statement = $this->db->prepare($sql);

        if (!$statement) return false;

        //Bind values
        foreach ($values as $key => $value) {
            if ($value === null) {
                $statement->bindValue(":$key", null, PDO::PARAM_INT);
            } elseif (is_array($value) || is_object($value)) {
                $statement->bindValue(":$key", json_encode($value));
            } else {
                $statement->bindValue(":$key", $value);
            }
        }

        if (!$statement->execute()) {
            return false;
        }

        return $this->db->lastInsertId();
    }

    /** Update row(s) in the database
     * @param string $table  The table to update
     * @param array|IDatabaseObject $data The data with new values, or IDatabaseObject
     * @param array $where The WHERE clause
     * @return bool True if successful, false otherwise.
     */
    protected function update($table, $data, $where)
    {

        if (is_object($data) && !($data instanceof IDatabaseObject)) return false;
        if (is_object($data)) $data = $data->toArray();

        //If where is just a number, assume it's an id
        if (is_numeric($where)) $where = ['id' => $where];

        //Avoid updating everything and stuff.
        if (!is_array($where) || count($where) == 0) return false;

        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $fieldString = join(', ', $fields);

        $whereString = $this->getWhereClause($where);

        $sql = "UPDATE `$table` SET {$fieldString} {$whereString}";

        //Fitler Values from Functions
        $values = [];
        foreach ($data as $key => $value) {
            if ($value === '%NOW%') {
                $sql = str_replace(":$key", 'NOW()', $sql);
            } elseif ($value === '%TIMESTAMP%') {
                $sql = str_replace(":$key", 'UNIX_TIMESTAMP()', $sql);
            } else {
                $values[$key] = $value;
            }
        }

        $statement = $this->db->prepare($sql);

        if (!$statement) return false;

        //Bind values
        foreach ($values as $key => $value) {
            if ($value === null) {
                $statement->bindValue(":$key", null, PDO::PARAM_INT);
            } elseif (is_array($value) || is_object($value)) {
                $statement->bindValue(":$key", json_encode($value));
            } else {
                $statement->bindValue(":$key", $value);
            }
        }

        //bind Where
        $this->bindWhereClause($statement, $where);

        return $statement->execute();
    }

    /** Deletes records.
     * @param string $table The name of the table.
     * @param array $where The WHERE clause [columns => value]
     * @param string $whereAddon Optional SQL text that will be pasted at the end of the where clause.
     * @return bool True on success, false on error.
     */
    protected function deleteRecords($table, $where = [], $whereAddon = null)
    {
        $whereString = $this->getWhereClause($where);

        $sql = "DELETE FROM `{$table}` {$whereString}";

        if ($whereAddon !== null) $sql .= 'AND ' . $whereAddon;

        $statement = $this->db->prepare($sql);

        if (!$statement) return false;

        $this->bindWhereClause($statement, $where);

        return $statement->execute();
    }

    protected function getValue($table, $column, $where, $whereAddon = null){
        $record = $this->getRecord($table, [$column], $where, $whereAddon);

        if (!$record || !isset($record[$column])) return false;

        return $record[$column];
    }

    private function getOrderString($orderArray){
        //Change a simple string into an array
        if (is_string($orderArray) && !empty($orderArray)) $orderArray = [$orderArray];

        //Check if this is indeed an array (now)
        if (!is_array($orderArray) || count($orderArray) == 0) return '';

        $string = "ORDER BY " . implode(', ', $orderArray);

        return $string;
    }

    private function getWhereClause($where){
        if (is_numeric($where)) $where = ['id' => $where];

        if (empty($where)) return "";

        $whereFields = [];
        foreach ($where as $key => $value) {
            $whereFields[] = "$key = :$key";
        }
        $whereString = 'WHERE ' . join(' AND ', $whereFields);

        return $whereString;
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