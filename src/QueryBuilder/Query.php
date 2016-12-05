<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 09/10/2016
 * Time: 16:41
 */

namespace c00\QueryBuilder;

/**
 * Class Query
 *
 * By now just a wrapper class for the easier to use `Qry`. This class will disappear in version 2.
 * @package src\QueryBuilder
 * @deprecated Use Qry instead. It's faster
 */
class Query implements IQry
{
    /** @var Qry */
    private $q;

    public function __construct()
    {

    }

    /**
     * @param array $columns
     * @param bool $distinct
     * @return Query
     */
    public function select($columns = [], $distinct = false)
    {
        $this->q = Qry::select($columns, $distinct);
        return $this;
    }

    /**
     * @param $table
     * @param $object
     * @return Query
     */
    public function insert($table, $object)
    {
        $this->q = Qry::insert($table, $object);
        return $this;
    }

    /**
     * @param $table
     * @param $object
     * @param array $where
     * @return Query
     */
    public function update($table, $object, array $where = [])
    {
        $this->q = Qry::update($table, $object, $where);
        return $this;
    }

    /**
     * @param null $table
     * @return Query
     */
    public function delete($table = null)
    {
        $this->q = Qry::delete($table);
        return $this;
    }

    /** Shorthand version for select, from, where.
     *
     * Useful for when you simply need to select from just one table with a simple where statement.
     *
     * @param $table string Table name
     * @param array $where
     * @return Query
     */
    public static function newSelect($table, array $where = []){
        $q = new Query();
        $q->select()
            ->from($table);
        foreach ($where as $column => $value) {
            $q->where($column, '=', $value);
        }

        return $q;
    }

    public function getType(){
        return $this->q->getType();
    }

    /**
     * @param $column
     * @param bool $ascending
     * @return Query
     */
    public function orderBy($column, $ascending = true)
    {
        $this->q->orderBy($column, $ascending);
        return $this;
    }

    public function getSql(&$params = null)
    {
        return $this->q->getSql($params);
    }

    public function getParams()
    {
        return $this->q->getParams();
    }

    public function getUpdateParams()
    {
        return $this->q->getParams();
    }

    public function getInsertParams()
    {
        return $this->q->getParams();
    }

    /**
     * @param $limit
     * @param int $offset
     * @return Query
     */
    public function limit($limit, $offset = 0)
    {
        $this->q->limit($limit, $offset);
        return $this;
    }

    /**
     * @param $className
     * @return Query
     */
    public function asClass($className)
    {
        $this->q->asClass($className);
        return $this;
    }

    public function getClass()
    {
        return $this->q->getClass();
    }

    /**
     * @param $column
     * @return Query
     */
    public function max($column)
    {
        if (!$this->q){
            $this->q = Qry::select();
        }
        $this->q->max($column);
        return $this;
    }

    /**
     * @param $function
     * @param $column
     * @param null $alias
     * @return Query
     */
    public function selectFunction($function, $column, $alias = null)
    {
        if (!$this->q){
            $this->q = Qry::select();
        }
        $this->q->selectFunction($function, $column, $alias);
        return $this;
    }

    /**
     * @param $tables
     * @return Query
     */
    public function from($tables)
    {
        $this->q->from($tables);
        return $this;
    }

    /**
     * @param $table
     * @param $column1
     * @param $operator
     * @param $column2
     * @return Query
     */
    public function join($table, $column1, $operator, $column2)
    {
        $this->q->join($table, $column1, $operator, $column2);
        return $this;
    }

    /**
     * @param $table
     * @param $column1
     * @param $operator
     * @param $column2
     * @param string $direction
     * @return Query
     */
    public function outerJoin($table, $column1, $operator, $column2, $direction = "LEFT")
    {
        $this->q->outerJoin($table, $column1, $operator, $column2, $direction);
        return $this;
    }

    /**
     * @param $condition1
     * @param $operator
     * @param $condition2
     * @return Query
     */
    public function where($condition1, $operator, $condition2)
    {
        $this->q->where($condition1, $operator, $condition2);
        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return Query
     */
    public function whereIn($column, array $values)
    {
        $this->q->whereIn($column, $values);
        return $this;
    }

    public function whereCount()
    {
        $this->q->whereCount();
        return $this;
    }

    public function checkDataType($object, $allowedTypes)
    {
        if (!$this->q){
            $this->q = Qry::select();
        }
        return $this->q->checkDataType($object, $allowedTypes);
    }


}