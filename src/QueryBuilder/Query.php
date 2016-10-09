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

    public function select($columns = [], $distinct = false)
    {
        $this->q = Qry::select($columns, $distinct);
        return $this;
    }

    public function insert($table, $object)
    {
        $this->q = Qry::insert($table, $object);
        return $this;
    }

    public function update($table, $object, array $where = [])
    {
        $this->q = Qry::update($table, $object, $where);
        return $this;
    }

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

    public function orderBy($column, $ascending = true)
    {
        $this->q->orderBy($column, $ascending);
        return $this;
    }

    public function getSql(&$params = null)
    {
        return $this->q->getSql($params);
    }

    public function getWhereParams()
    {
        return $this->q->getWhereParams();
    }

    public function getUpdateParams()
    {
        return $this->q->getUpdateParams();
    }

    public function getInsertParams()
    {
        return $this->q->getInsertParams();
    }

    public function limit($limit, $offset = 0)
    {
        $this->q->limit($limit, $offset);
        return $this;
    }

    public function asClass($className)
    {
        $this->q->asClass($className);
        return $this;
    }

    public function getClass()
    {
        return $this->q->getClass();
    }

    public function max($column)
    {
        if (!$this->q){
            $this->q = Qry::select();
        }
        $this->q->max($column);
        return $this;
    }

    public function selectFunction($function, $column, $alias = null)
    {
        if (!$this->q){
            $this->q = Qry::select();
        }
        $this->q->selectFunction($function, $column, $alias);
        return $this;
    }

    public function from($tables)
    {
        $this->q->from($tables);
        return $this;
    }

    public function join($table, $column1, $operator, $column2)
    {
        $this->q->join($table, $column1, $operator, $column2);
        return $this;
    }

    public function outerJoin($table, $column1, $operator, $column2, $direction = "LEFT")
    {
        $this->q->outerJoin($table, $column1, $operator, $column2, $direction);
        return $this;
    }

    public function where($condition1, $operator, $condition2)
    {
        $this->q->where($condition1, $operator, $condition2);
        return $this;
    }

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