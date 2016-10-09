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
        $this->q = new Qry();
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
        return $this->q->orderBy($column, $ascending);
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
        return $this->q->limit($limit, $offset);
    }

    public function asClass($className)
    {
        return $this->q->asClass($className);
    }

    public function getClass()
    {
        return $this->q->getClass();
    }

    public function max($column)
    {
        return $this->q->max($column);
    }

    public function selectFunction($function, $column, $alias = null)
    {
        return $this->q->selectFunction($function, $column, $alias);
    }

    public function from($tables)
    {
        return $this->q->from($tables);
    }

    public function join($table, $column1, $operator, $column2)
    {
        return $this->q->join($table, $column1, $operator, $column2);
    }

    public function outerJoin($table, $column1, $operator, $column2, $direction = "LEFT")
    {
        return $this->q->outerJoin($table, $column1, $operator, $column2, $direction);
    }

    public function where($condition1, $operator, $condition2)
    {
        return $this->q->where($condition1, $operator, $condition2);
    }

    public function whereIn($column, array $values)
    {
        return $this->q->whereIn($column, $values);
    }

    public function whereCount()
    {
        return $this->q->whereCount();
    }

    public function checkDataType($object, $allowedTypes)
    {
        return $this->q->checkDataType($object, $allowedTypes);
    }


}