<?php

namespace c00\QueryBuilder;

use c00\common\Helper as H;
use c00\common\IDatabaseObject;
use c00\QueryBuilder\components\Comparison;
use c00\QueryBuilder\components\From;
use c00\QueryBuilder\components\FromClass;
use c00\QueryBuilder\components\FromClause;
use c00\QueryBuilder\components\Join;
use c00\QueryBuilder\components\JoinClass;
use c00\QueryBuilder\components\JoinClause;
use c00\QueryBuilder\components\OrderByClause;
use c00\QueryBuilder\components\SelectClause;
use c00\QueryBuilder\components\SelectFunction;
use c00\QueryBuilder\components\UpdateClause;
use c00\QueryBuilder\components\WhereClause;
use c00\QueryBuilder\components\WhereGroup;
use c00\QueryBuilder\components\WhereIn;
use c00\QueryBuilder\components\Where;

class Qry implements IQry
{
    const TYPE_SELECT = 'select';
    const TYPE_UPDATE = 'update';
    const TYPE_INSERT = 'insert';
    const TYPE_DELETE = 'delete';

    /** @var string For debug purposes this gets filled after getSql(). */
    public $sql;

    /** @var SelectClause */
    private $_select;
    /** @var FromClause */
    private $_from;

    private $_limit = 0, $_offset = 0, $_object;

    /** @var WhereClause */
    private $_where;

    /** @var UpdateClause */
    private $_update;
    private $_insert;
    private $_type;

    /** @var  JoinClause */
    private $_join;
    /** @var  OrderByClause */
    private $_orderBy;
    private $_groupBy = [];
    private $_having = [];

    //Params
    /** @var ParamStore */
    private $paramStore;
    //todo: Remove all these other params things.
    private $_insertParams = [];
    private $_havingParams = [];

    private $_returnClass = '';
    
    public function __construct()
    {
        $this->_select = new SelectClause();
        $this->_update = new UpdateClause();
        $this->_from = new FromClause();
        $this->_join = new JoinClause();
        $this->_where = new WhereClause();
        $this->_orderBy = new OrderByClause();
        $this->paramStore = new ParamStore();
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

        $table = QryHelper::encap($table);

        $q->_insert = "INSERT INTO $table";
        $q->_object = $object;

        $q->_type = self::TYPE_INSERT;
        return $q;
    }

    /**
     * @param array|string $columns
     * @param bool $distinct
     * @return Qry
     */
    public static function select($columns = [], $distinct = false){
        $q = new Qry();

        $q->_select->addColumns($columns);
        $q->_select->distinct = $distinct;
        $q->_type = self::TYPE_SELECT;

        return $q;
    }

    /** Selects grouped ranges.
     *
     * Used when you need to group ranges together, such as dates.
     *
     * @param Ranges|\c00\QueryBuilder\components\Ranges $ranges
     * @return Qry
     */
    public static function selectRange($ranges){
        $q = self::select();
        $q->_select->addSelect($ranges);
        $q->groupBy($ranges->alias);

        return $q;
    }

    /**
     * @param $table string|array
     * @param $object
     * @param array $where
     * @return Qry
     * @throws \Exception
     */
    public static function update($table, $object, array $where = []){
        $q = new Qry();

        //Throw an error if it's not something I can `toArray`
        $q->checkDataType($object, [IDatabaseObject::class, 'array']);

        //Set table name
        $q->checkDataType($table, ['string', 'array']);
        if (is_string($table)) $table = [$table];
        if (count($table) > 1) {
            throw new \Exception("Update should only have one table. Use Joins to update multiple tables.");
        }

        foreach ($table as $key => $name) {
            $alias = (is_numeric($key)) ? null : $key;

            $q->_update->alias = $alias;
            $q->_update->table = $name;
        }

        $q->_update->setObject($object);

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
        $this->_orderBy->addColumn($column, $ascending);

        return $this;
    }


    /** Add ORDER BY $column IS NULL
     * Orders by rather the column value is NULL. Ascending will put them at the bottom, descending at the top.
     * @param $column
     * @param $ascending
     * @return $this
     */
    public function orderByNull($column, $ascending = true) {
        $this->_orderBy->addColumn($column, $ascending, 'IS NULL');

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
            throw new QueryBuilderException("Group by requires a string or array");
        }

        $this->_groupBy = array_merge($this->_groupBy, $columns);

        return $this;
    }

    public function having($condition1, $operator, $condition2){
        $condition = [
            'condition1' => $condition1,
            'operator' => $operator,
            'condition2' => $condition2
        ];

        $this->_having[] = $condition;


        return $this;
    }

	/** Get the SQL needed to prepare the query.
	 * This will return an SQL query that can be used with PDO::prepare()
	 *
	 * @param array $params The parameters fort he prepared statement.
	 *
	 * @return string
	 * @throws QueryBuilderException
	 * @throws \Exception
	 */
    public function getSql(&$params = null){
        if ($this->_type == self::TYPE_SELECT){
            $sql = $this->getSelectString() .
                $this->_from->toString($this->paramStore) .
                $this->_join->toString($this->paramStore) .
                $this->getWhereString() .
                $this->getGroupByString() .
                $this->getHavingString() .
                $this->getOrderByString() .
                $this->getLimit();

        } else if ($this->_type == self::TYPE_UPDATE){
            $sql = $this->_update->toString() . $this->_join->toString() . $this->_update->getSetString($this->paramStore) . $this->getWhereString();
        } else if ($this->_type == self::TYPE_INSERT){
            $sql = $this->_insert . $this->getInsertString();
        } else if ($this->_type == self::TYPE_DELETE){
            $sql = $this->buildDeleteSql();
        } else {
            throw new QueryBuilderException("Not implemented");
        }

        $params = $this->getParams();

        $this->sql = $sql;

        return $sql;
    }

    private function buildDeleteSql(){
        //If there's a join, we need some stuff at the beginning.
        $tableString = '';
        if ($this->_join->hasAny()) {
            $tableString = ' ' . implode(', ', $this->_from->getTableNames());
        }

        $sql = "DELETE" . $tableString . $this->_from->toString() . $this->_join->toString() . $this->getWhereString();

        return $sql;
    }


    public function getParams(){
        //todo: Refactor so everything uses the ParamStore
        return array_merge($this->_insertParams, $this->paramStore->getParams(), $this->_havingParams);
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

    /** Gets the class set by fromClass() or asClass()
     * @return string
     */
    public function getClass(){
        $fromClass = $this->_from->getTableWithClass();
        if ($fromClass) return $fromClass->class;

        return $this->_returnClass;
    }

    /**
     * Gets the class mapping. The first entry is from fromClass.
     * @return array ['alias' => className, ...]
     */
    public function getClasses() {
        $fromClass = $this->_from->getTableWithClass();

        $array = [ $fromClass->alias => $fromClass->class ];

        foreach ($this->_join->getJoinsWithClass() as $join) {
            $array[$join->alias] = $join->class;
        }

        return $array;
    }

    public function hasNestedClasses() {
        return ($this->_from->getTableWithClass() !== null);
    }

    public function getType(){
        return $this->_type;
    }

    //region StatisticFunctions
    /**
     * @param $column
     * @param string $alias
     * @param $keyword string
     * @return Qry
     */
    public function max($column, $alias = null, $keyword = null){
        return $this->selectFunction("MAX", $column, $alias, $keyword);
    }

    /**
     * @param $column
     * @param string $alias
     * @param $keyword string
     * @return Qry
     */
    public function min($column, $alias = null, $keyword = null){
        return $this->selectFunction("MIN", $column, $alias, $keyword);
    }

    /**
     * @param $column
     * @param string $alias
     * @param $keyword string
     * @return Qry
     */
    public function sum($column, $alias = null, $keyword = null){
        return $this->selectFunction("SUM", $column, $alias, $keyword);
    }

    /**
     * @param $column
     * @param string $alias
     * @param $keyword string
     * @return Qry
     */
    public function count($column, $alias = null, $keyword = null){
        return $this->selectFunction("COUNT", $column, $alias, $keyword);
    }

    /**
     * @param $column
     * @param string $alias
     * @param $keyword string
     * @return Qry
     */
    public function avg($column, $alias = null, $keyword = null){
        return $this->selectFunction("AVG", $column, $alias, $keyword);
    }


    public function groupConcat($column, $alias = null, $keyword = null, $separator = null) {
    	$params = null;
    	if ($separator) {
    		$params = ["SEPARATOR" => $separator];
	    }
        return $this->selectFunction("GROUP_CONCAT", $column, $alias, $keyword, $params);
    }

	/**
	 * @param $function string e.g. count
	 * @param $column string e.g. id
	 * @param null|string $alias e.g. wordCount
	 * @param null|string $keyword e.g. DISTINCT
	 *
	 * @param null|array|string $params Parameters for the function. (e.g. GROUP_CONCAT($column, ["SEPARATOR" => ", "])
	 *
	 * @return Qry
	 * @throws QueryBuilderException
	 */
    public function selectFunction($function, $column, $alias = null, $keyword = null, $params = null){

        $f = new SelectFunction($function, $column, $alias, $keyword, $params);

        $this->_select->addSelect($f);

        return $this;
    }
    //endregion

    /**
     * @param $tables
     * @return Qry
     */
    public function from($tables){
        /*
         * Tables come in the shape of
         *    ['alias' => 'tableName', ...]
         * or ['tablename', ...]
         * or ['alias' => 'tableName', 'tablename', ...]
         *
         * Combinations are possible.
         */

        $this->checkDataType($tables, ['string', 'array']);

        //Normalize to array
        if (is_string($tables)) $tables = [$tables];

        foreach ($tables as $key => $table) {
            $alias = (is_numeric($key)) ? null : $key;

            $this->_from->tables[] = From::newFrom($table, $alias);
        }

        return $this;
    }


    /**
     * @param $class string The class name
     * @param $table string The database table
     * @param $alias string The alias used in the SQL query
     * @return $this
     * @throws QueryBuilderException Occurs when trying to add a second fromClass
     */
    public function fromClass($class, $table, $alias){
        $this->checkDataTypes([$class, $table, $alias], 'string');

        if ($this->_from->getTableWithClass()) {
            throw new QueryBuilderException("Can only have one class in FROM clause.");
        }

        $this->_from->tables[] = FromClass::newFromClass($class, $table, $alias);

        return $this;
    }

    /**
     * @param $table string|array
     * @param $column1 string e.g. user.firstName
     * @param $operator string e.g. =, <, >, LIKE, IS, IS NOT
     * @param $column2 string e.g. 'lisa'
     * @return Qry
     */
    public function join($table, $column1, $operator, $column2){

        $alias = null;
        if (is_array($table)){
            reset($table);
            $key = key($table);
            if (!is_numeric($key)) $alias = $key;

            //Make table to be a string.
            $table = $table[$key];
        }

        $join = Join::newJoin($table, $alias, $column1, $operator, $column2);

        $this->_join->joins[] = $join;

        return $this;
    }

    /**
     * Add a Join Object to the query.
     * Useful in situation where you want to add multiple ON parts to the join.
     * @param $join Join The Join object
     * @return $this
     */
    public function addJoin($join) {
        $this->_join->joins[] = $join;

        return $this;
    }

    /**
     * @param $class string
     * @param $table string
     * @param $alias string
     * @param $column1 string
     * @param $operator string
     * @param $column2 string
     * @return $this
     */
    public function joinClass($class, $table, $alias, $column1, $operator, $column2){
        //todo For version 1.0, consolidate the signatures. either use the array format or don't.

        $join = JoinClass::newJoinClass($class, $table, $alias, $column1, $operator, $column2);

        $this->_join->joins[] = $join;

        return $this;
    }

    /**
     * @param $table string|array
     * @param $column1 string
     * @param $operator string
     * @param $column2 string
     * @param string $direction
     * @return Qry
     */
    public function outerJoin($table, $column1, $operator, $column2, $direction = "LEFT"){

        $alias = "";
        if (is_array($table)){
            reset($table);
            $key = key($table);
            if (!is_numeric($key)) $alias = $key;

            //Make table to be a string.
            $table = $table[$key];
        }

        $join = Join::newOuterJoin($table, $alias, $column1, $operator, $column2, $direction);

        $this->_join->joins[] = $join;

        return $this;
    }

    /**
     * @param $class string
     * @param $table string
     * @param $alias string
     * @param $column1 string
     * @param $operator string
     * @param $column2 string
     * @param string $direction
     * @return $this
     */
    public function outerJoinClass($class, $table, $alias, $column1, $operator, $column2, $direction = "LEFT"){

        $join = JoinClass::newOuterJoinClass($class, $table, $alias, $column1, $operator, $column2, $direction);

        $this->_join->joins[] = $join;

        return $this;
    }

    /**
     * @param $condition1
     * @param $operator
     * @param $condition2
     * @return Qry
     */
    public function where($condition1, $operator, $condition2){
        $condition = Where::new($condition1, $operator, $condition2);

        $this->_where->conditions[] = $condition;

        return $this;
    }

    /**
     * @param $condition1
     * @param $operator
     * @param $condition2
     * @return Qry
     */
    public function orWhere($condition1, $operator, $condition2){
        $condition = Where::new($condition1, $operator, $condition2, Comparison::TYPE_OR);

        $this->_where->conditions[] = $condition;

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return Qry
     */
    public function whereIn($column, array $values){

        $wi = WhereIn::new($column, $values);
        if ($this->_where->isEmpty()) $wi->isFirst = true;

        $this->_where->conditions[] = $wi;

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return Qry
     */
    public function whereNotIn($column, array $values){

        $wi = WhereIn::new($column, $values);
        $wi->isNotIn = true;
        if ($this->_where->isEmpty()) $wi->isFirst = true;

        $this->_where->conditions[] = $wi;

        return $this;
    }

    /**
     * @param $group WhereGroup
     * @return Qry
     */
    public function whereGroup($group){
        $this->_where->conditions[] = $group;
        return $this;
    }

    /**
     * @param $group WhereGroup
     * @return Qry
     */
    public function orWhereGroup($group){
        $group->type = Comparison::TYPE_OR;
        $this->_where->conditions[] = $group;
        return $this;
    }

    public function whereCount(){
        return count($this->_where->conditions);
    }


    public function checkDataTypes($objects, $allowedTypes) {
        $this->checkDataType($objects, 'array');

        foreach ($objects as $object) {
            $this->checkDataType($object, $allowedTypes);
        }

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

        $this->_select->addClassColumns($this->_from->getTableWithClass(), $this->_join->getJoinsWithClass());

        return $this->_select->toString($this->paramStore);
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
            throw new \Exception("Unsupported data type for insert");
        }

        if (count($array) == 0){
            throw new \Exception("Nothing to save!");
        }

        $this->_insertParams = [];
        $columns = [];
        $values = [];
        foreach ($array as $key => $value) {
            $paramId = H::getUniqueId($this->_insertParams);
            $this->_insertParams[$paramId] = $value;

            $columns[] = $key;
            $values[] = ":$paramId";
        }

        return " (`" . implode('`, `', $columns) . '`) VALUES(' . implode(', ', $values) . ')';
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
        return $this->_orderBy->toString();
    }

    private function getGroupByString(){
        if (count($this->_groupBy) == 0) return '';

        $strings = [];
        foreach ($this->_groupBy as $item) {
            $strings[] = QryHelper::encap($item);
        }

        return " GROUP BY " . implode(', ', $strings);
    }

    private function getHavingString(){
        if (count($this->_having) == 0) return '';

        $this->_havingParams = [];
        $strings = [];
        foreach ($this->_having as $condition) {
            $condition['condition1'] = QryHelper::encap($condition['condition1']);

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

            $conditionId = H::getUniqueId($this->_havingParams);
            $this->_havingParams[$conditionId] = $condition['condition2'];

            $strings[] = "{$condition['condition1']} {$condition['operator']} :$conditionId";
        }


        return " HAVING " . implode(' AND ', $strings);
    }

    private function shouldEscape(array &$condition){
        $check = $condition['condition2'];
        if (substr($check, 0, 2) == '**'){
            $condition['condition2'] = QryHelper::encap(substr($check, 2));
            return false;
        }

        return true;
    }

    private function getWhereString(){
        return $this->_where->toString($this->paramStore);
    }

    #endregion
}