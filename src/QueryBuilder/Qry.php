<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 17/06/2016
 * Time: 10:26
 */

namespace c00\QueryBuilder;


use c00\common\IDatabaseObject;

class Qry implements IQry
{
    const TYPE_SELECT = 'select';
    const TYPE_UPDATE = 'update';
    const TYPE_INSERT = 'insert';
    const TYPE_DELETE = 'delete';

    /** @var string For debug purposes this gets filled after getSql(). */
    public $sql;

    private $_select = [];
    private $_distinct = false;
    private $_from = [];

    private $_limit = 0, $_offset = 0, $_object;

    private $_where = [], $_whereParams = [];
    private $_whereIn = [];
    private $_update, $_updateParams = [];
    private $_insert, $_insertParams = [];
    private $_type;
    private $_join = '';
    private $_orderBy = [];
    private $_groupBy = [];

    private $_returnClass = '';
    
    public function __construct()
    {

    }

    #region statics
    /**
     * @param $table
     * @param $object
     * @return Qry
     */
    public static function insert($table, $object){
        $q = new Qry;

        //Throw an error if it's not something I can toArray
        $q->checkDataType($object, [IDatabaseObject::class, 'array']);

        $table = $q->encap($table);

        $q->_insert = "INSERT INTO $table";
        $q->_object = $object;

        $q->_type = self::TYPE_INSERT;
        return $q;
    }

    /**
     * @param array $columns
     * @param bool $distinct
     * @return Qry
     */
    public static function select($columns = [], $distinct = false){
        $q = new Qry();

        //Normalize to array
        if (is_string($columns)) $columns = [$columns];


        if (count($columns) == 1 && $columns[0] == '*') {
            //Just empty it. We convert it into * later on if necessary.
            $columns = [];
        }

        //encap column names.
        foreach ($columns as $alias => &$column) {
            $column = $q->encap($column);
        }

        $q->_distinct = $distinct;
        $q->_select = $columns;
        $q->_type = self::TYPE_SELECT;
        return $q;

        //At the end we end up with an array that goed alias => `table`.`column` for each column.
    }

    /**
     * @param $table
     * @param $object
     * @param array $where
     * @return Qry
     */
    public static function update($table, $object, array $where = []){
        $q = new Qry();

        //Throw an error if it's not something I can `toArray`
        $q->checkDataType($object, [IDatabaseObject::class, 'array']);

        $table = $q->encap($table);
        $q->_update = "UPDATE $table";
        $q->_object = $object;

        $q->_type = self::TYPE_UPDATE;

        //Where
        foreach ($where as $key => $value) {
            $q->where($key, '=', $value);
        }

        return $q;
    }

    /**
     * @param null $table
     * @return Qry
     */
    public static function delete($table = null){
        $q = new Qry();

        if ($table) $q->from($table);

        $q->_type = self::TYPE_DELETE;
        return $q;
    }
    #endregion

    #region public
    /** Add ORDER BY clause.
     * to order on more than one column, call orderBy() several times.
     * @param $column string column to add to ordering.
     * @param bool $ascending
     * @return Qry
     */
    public function orderBy($column, $ascending = true){
        $this->_orderBy[] = ['column' => $column, 'asc' => $ascending];

        return $this;
    }

    /** Add GROUP BY clause.
     *
     * @param $columns array|string column(s) to group by.
     * @return Qry
     * @throws QueryBuilderException
     */
    public function groupBy($columns){
        if (is_string($columns)) $columns = [$columns];
        if (!is_array($columns)){
            throw new QueryBuilderException("Group by requries a string or array");
        }

        $this->_groupBy = array_merge($this->_groupBy, $columns);

        return $this;
    }

    public function getSql(&$params = null){
        if ($this->_type == self::TYPE_SELECT){
            $sql = $this->getSelectString() . $this->getFromString() . $this->_join . $this->getWhereString() . $this->getGroupByString() . $this->getOrderByString() . $this->getLimit();
            $params = $this->_whereParams;
        } else if ($this->_type == self::TYPE_UPDATE){
            $sql = $this->_update . $this->getSetString() . $this->getWhereString();
            $params = array_merge($this->_updateParams, $this->_whereParams);
        } else if ($this->_type == self::TYPE_INSERT){
            $sql = $this->_insert . $this->getInsertString();
            $params = array_merge($this->_insertParams, $this->_whereParams);
        } else if ($this->_type == self::TYPE_DELETE){
            $sql = "DELETE" . $this->getFromString() . $this->getWhereString();
            $params = array_merge($this->_insertParams, $this->_whereParams);
        } else {
            throw new QueryBuilderException("Not implemented");
        }

        $this->sql = $sql;

        return $sql;
    }


    public function getWhereParams(){
        return $this->_whereParams;
    }

    public function getUpdateParams(){
        return $this->_updateParams;
    }

    public function getInsertParams(){
        return $this->_insertParams;
    }

    /**
     * @param $limit
     * @param int $offset
     * @return Qry
     */
    public function limit($limit, $offset = 0){
        $this->checkDataType($limit, 'numeric');
        $this->checkDataType($offset, 'numeric');

        $this->_limit = $limit;
        $this->_offset = $offset;

        return $this;
    }

    /**
     * @param $className
     * @return Qry
     */
    public function asClass($className){
        $this->_returnClass = $className;

        return $this;
    }

    public function getClass(){
        return $this->_returnClass;
    }

    public function getType(){
        return $this->_type;
    }

    /**
     * @param $column
     * @param string $alias
     * @return Qry
     */
    public function max($column, $alias = null){
        return $this->selectFunction("MAX", $column, $alias);
    }

    /**
     * @param $column
     * @param string $alias
     * @return Qry
     */
    public function sum($column, $alias = null){
        return $this->selectFunction("SUM", $column, $alias);
    }

    /**
     * @param $column
     * @param string $alias
     * @return Qry
     */
    public function min($column, $alias = null){
        return $this->selectFunction("MIN", $column, $alias);
    }



    /**
     * @param $column
     * @param string $alias
     * @return Qry
     */
    public function count($column, $alias = null){
        return $this->selectFunction("COUNT", $column, $alias);
    }

    /**
     * @param $column
     * @param string $alias
     * @return Qry
     */
    public function avg($column, $alias = null){
        return $this->selectFunction("AVG", $column, $alias);
    }

    /**
     * @param $function
     * @param $column
     * @param null $alias
     * @return Qry
     */
    public function selectFunction($function, $column, $alias = null){
        $column = "$function({$this->encap($column)})";

        if ($alias){
            $this->_select[$alias] = $column;
        } else {
            $this->_select[] = $column;
        }

        return $this;
    }

    /**
     * @param $tables
     * @return Qry
     */
    public function from($tables){
        $this->checkDataType($tables, ['string', 'array']);

        //Normalize to array
        if (is_string($tables)) $tables = [$tables];

        $tables = $this->encapArray($tables);

        $this->_from = array_merge($this->_from, $tables);

        return $this;
    }

    public function getFromString(){
        $tables = [];
        foreach ($this->_from as $alias => $table) {
            $string = $table;
            if (!is_numeric($alias)) $string .= " AS `$alias`";
            $columnStrings[] = $string;
            $tables[] = $string;
        }


        if (count($tables) == 0) {
            throw new QueryBuilderException("No FROM clause!");
        }

        return " FROM " . implode(', ', $tables);
    }

    /**
     * @param $table
     * @param $column1
     * @param $operator
     * @param $column2
     * @return Qry
     */
    public function join($table, $column1, $operator, $column2){
        $alias = "";
        if (is_array($table)){
            reset($table);
            $key = key($table);
            if (!is_numeric($key)) $alias = " AS ". $this->encap($key);

            //Make table to be a string.
            $table = $table[$key];
        }

        $column1 = $this->encap($column1);
        $column2 = $this->encap($column2);
        $table = $this->encap($table);

        $this->_join .= " JOIN {$table}{$alias} ON $column1 $operator $column2";

        return $this;
    }

    /**
     * @param $table
     * @param $column1
     * @param $operator
     * @param $column2
     * @param string $direction
     * @return Qry
     */
    public function outerJoin($table, $column1, $operator, $column2, $direction = "LEFT"){

        $column1 = $this->encap($column1);
        $column2 = $this->encap($column2);

        $this->_join .= " $direction OUTER JOIN `$table` ON $column1 $operator $column2";

        return $this;
    }

    /**
     * @param $condition1
     * @param $operator
     * @param $condition2
     * @return Qry
     */
    public function where($condition1, $operator, $condition2){
        $condition = [
            'condition1' => $condition1,
            'operator' => $operator,
            'condition2' => $condition2
        ];

        $this->_where[] = $condition;

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return Qry
     */
    public function whereIn($column, array $values){
        if (count($values) == 0) return $this;


        $this->_whereIn[] = [
            'column' => $column,
            'values' => $values
        ];

        return $this;
    }

    public function whereCount(){
        return count($this->_where);
    }


    public function checkDataType($object, $allowedTypes){
        //CHECKING
        if (!is_string($allowedTypes) && ! is_array($allowedTypes)) {
            throw new QueryBuilderException("AllowedTypes is expecting a string or Array. Instead, got " . get_class($allowedTypes), 9);
        }

        //Make array
        if (is_string($allowedTypes)) $allowedTypes = [$allowedTypes];

        foreach ($allowedTypes as $type) {
            $check = "is_" . $type;
            if (function_exists($check) && $check($object)){
                //e.g. is_string();
                return true;
            }

            //Check class and interfaces.
            if (is_object($object) &&
                (class_exists($type) || interface_exists($type)) &&
                $object instanceof $type){
                return true;
            }
        }

        //Is object?
        if (is_object($object)){
            $class = get_class($object);
        } else {
            $class = gettype($object);
        }
        throw new QueryBuilderException("Wrong Datatype. Expecting: [" . implode(', ', $allowedTypes) . "]. Got: " . $class, 10);
    }

    #endregion

    #region private
    private function getSelectString(){
        //Add aliases too.

        $columns = [];
        foreach ($this->_select as $alias => $column) {
            $string = $column;
            if (!is_numeric($alias)) $string .= " AS `$alias`";
            $columnStrings[] = $string;
            $columns[] = $string;
        }

        //If we have nothing, show the star!
        if (count($columns) == 0) $columns[] = "*";

        $distinct = ($this->_distinct)? "DISTINCT " : "";

        return "SELECT $distinct" . implode(', ', $columns);
    }

    private function getInsertString(){
        /** @var IDatabaseObject $o */
        $o = $this->_object;

        if (is_object($this->_object)){
            $this->checkDataType($o, IDatabaseObject::class);
            $array = $o->toArray();
        } elseif (is_array($this->_object)){
            $array = $this->_object;
        } else {
            throw new \Exception("Unsupported datatype for insert");
        }

        if (count($array) == 0){
            throw new \Exception("Nothing to save!");
        }

        $this->_insertParams = [];
        $columns = [];
        $values = [];
        foreach ($array as $key => $value) {
            $paramId = uniqid();
            $this->_insertParams[$paramId] = $value;

            $columns[] = $key;
            $values[] = ":$paramId";
        }

        return " (`" . implode('`, `', $columns) . '`) VALUES(' . implode(', ', $values) . ')';
    }

    private function getSetString(){
        /** @var IDatabaseObject $o */
        $o = $this->_object;

        $this->checkDataType($o, [IDatabaseObject::class, 'array']);
        if (is_array($o)){
            $array = $o;
        }else {
            $array = $o->toArray();
        }


        if (count($array) == 0){
            throw new \Exception("Nothing to set!");
        }

        $this->_updateParams = [];
        $strings = [];
        foreach ($array as $key => $value) {
            $paramId = uniqid();
            $this->_updateParams[$paramId] = $value;

            $strings[] = "`{$key}` = :$paramId";
        }

        return " SET " . implode(', ', $strings);
    }

    private function getLimit(){
        if ($this->_limit == 0) return '';
        $string = " LIMIT {$this->_limit}";

        if ($this->_offset > 0){
            $string .= " OFFSET {$this->_offset}";
        }

        return $string;
    }

    private function getOrderByString(){
        if (count($this->_orderBy) == 0) return '';

        $strings = [];
        foreach ($this->_orderBy as $item) {


            $string = $this->encap($item['column']);

            $string .= ($item['asc']) ? " ASC" : " DESC";
            $strings[] = $string;
        }

        return " ORDER BY " . implode(', ', $strings);
    }

    private function getGroupByString(){
        if (count($this->_groupBy) == 0) return '';

        $strings = [];
        foreach ($this->_groupBy as $item) {
            $strings[] = $this->encap($item);
        }

        return " GROUP BY " . implode(', ', $strings);
    }

    private function shouldEscape(array &$condition){
        $check = $condition['condition2'];
        if (substr($check, 0, 2) == '**'){
            $condition['condition2'] = $this->encap(substr($check, 2));
            return false;
        }

        return true;
    }

    private function getWhereString(){
        if (count($this->_where) == 0 && count($this->_whereIn) == 0) return '';

        $this->_whereParams = [];
        $strings = [];
        foreach ($this->_where as $condition) {
            $condition['condition1'] = $this->encap($condition['condition1']);

            //allow IS NULL and IS NOT NULL
            if ($condition['condition2'] === null){
                $strings[] = "{$condition['condition1']} {$condition['operator']} NULL";
                continue;
            }

            //Don't escape condition 2 starting with **
            if (!$this->shouldEscape($condition)){
                $strings[] = "{$condition['condition1']} {$condition['operator']} {$condition['condition2']}";
                continue;
            }

            $conditionId = uniqid();
            $this->_whereParams[$conditionId] = $condition['condition2'];

            $strings[] = "{$condition['condition1']} {$condition['operator']} :$conditionId";
        }

        foreach ($this->_whereIn as $condition) {
            //Every whereIn has a column and an array of values for the IN part.

            $condition['column'] = $this->encap($condition['column']);

            $inValues = [];
            foreach ($condition['values'] as $value) {
                $valueId = uniqid();
                $this->_whereParams[$valueId] = $value;
                $inValues[] = $valueId;
            }

            $inString = implode(', :', $inValues);

            if (count($inValues) > 0){
                $strings[] = "{$condition['column']} IN (:$inString)";
            }

        }

        return " WHERE " . implode(' AND ', $strings);
    }

    /** Turns   table.column   into   `table`.`column`
     * @param $string
     * @return string
     */
    private function encap($string){
        $string = str_replace('`', '', $string);

        $parts = explode('.', $string);
        foreach ($parts as &$part) {
            if ($part == '*') continue;
            $part = "`$part`";
        }
        $encapsulated = implode('.', $parts);

        return $encapsulated;
    }

    /** Turns   table.column   into   `table`.`column`
     * @param $array array takes an array of strings.
     * @return array
     */
    private function encapArray($array){
        foreach ($array as &$item) {
            $item = $this->encap($item);
        }


        return $array;
    }
    #endregion
}