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

    private $_select, $_from, $_limit = 0, $_offset = 0, $_object;

    private $_selectFunctions = [];
    private $_where = [], $_whereParams = [];
    private $_whereIn = [];
    private $_update, $_updateParams = [];
    private $_insert, $_insertParams = [];
    private $_type;
    private $_join = '';
    private $_orderBy = [];

    private $_returnClass = '';
    
    public function __construct()
    {

    }

    #region statics
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

    public static function select($columns = [], $distinct = false){
        $distinct = ($distinct) ? 'DISTINCT ' : '';
        $q = new Qry();

        if (is_string($columns)) $columns = [$columns];
        if (count($columns) == 0) $columns = ['*'];

        if (count($columns) == 1 && $columns[0] == '*') {
            $q->_select = "$distinct*";
        } else {
            $columnStrings = [];
            //encap and add column Alias to it
            foreach ($columns as $alias => $column) {
                $string = $q->encap($column);
                if (!is_numeric($alias)) $string .= " AS `$alias`";
                $columnStrings[] = $string;
            }

            $q->_select = "$distinct" . implode(', ', $columnStrings);
        }

        $q->_type = self::TYPE_SELECT;
        return $q;
    }

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
     * @param $column string colum to add to ordering.
     * @param bool $ascending
     * @return Qry
     */
    public function orderBy($column, $ascending = true){
        $this->_orderBy[] = ['column' => $column, 'asc' => $ascending];

        return $this;
    }

    public function getSql(&$params = null){
        if ($this->_type == self::TYPE_SELECT){
            $sql = $this->getSelectString() . $this->_from . $this->_join . $this->getWhereString() . $this->getOrderByString() . $this->getLimit();
            $params = $this->_whereParams;
        } else if ($this->_type == self::TYPE_UPDATE){
            $sql = $this->_update . $this->getSetString() . $this->getWhereString();
            $params = array_merge($this->_updateParams, $this->_whereParams);
        } else if ($this->_type == self::TYPE_INSERT){
            $sql = $this->_insert . $this->getInsertString();
            $params = array_merge($this->_insertParams, $this->_whereParams);
        } else if ($this->_type == self::TYPE_DELETE){
            $sql = "DELETE" . $this->_from . $this->getWhereString();
            $params = array_merge($this->_insertParams, $this->_whereParams);
        } else {
            throw new \Exception("Not implemented");
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

    public function limit($limit, $offset = 0){
        $this->checkDataType($limit, 'numeric');
        $this->checkDataType($offset, 'numeric');

        $this->_limit = $limit;
        $this->_offset = $offset;

        return $this;
    }

    public function asClass($className){
        $this->_returnClass = $className;

        return $this;
    }

    public function getClass(){
        return $this->_returnClass;
    }



    public function max($column){
        return $this->selectFunction("MAX", $column);
    }

    public function selectFunction($function, $column, $alias = null){
        if ($this->_type == null) $this->_type = self::TYPE_SELECT;

        $aliasString = ($alias) ? " as ". $this->encap($alias) : '';

        $this->_selectFunctions[] = "$function({$this->encap($column)})$aliasString";
        return $this;
    }



    public function from($tables){
        $this->checkDataType($tables, ['string', 'array']);

        //Make array
        if (is_string($tables)) $tables = [$tables];

        $tables = $this->encapArray($tables);

        $this->_from = " FROM " . implode(', ', $tables) . "";

        return $this;
    }

    public function join($table, $column1, $operator, $column2){

        $column1 = $this->encap($column1);
        $column2 = $this->encap($column2);

        $this->_join .= " JOIN `$table` ON $column1 $operator $column2";

        return $this;
    }

    public function outerJoin($table, $column1, $operator, $column2, $direction = "LEFT"){

        $column1 = $this->encap($column1);
        $column2 = $this->encap($column2);

        $this->_join .= " $direction OUTER JOIN `$table` ON $column1 $operator $column2";

        return $this;
    }

    public function where($condition1, $operator, $condition2){
        $condition = [
            'condition1' => $condition1,
            'operator' => $operator,
            'condition2' => $condition2
        ];

        $this->_where[] = $condition;

        return $this;
    }

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
        $parts = [];

        $parts = array_merge($parts, $this->_selectFunctions);
        if ($this->_select) $parts[] = $this->_select;

        return "SELECT " . implode(', ', $parts);
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