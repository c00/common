<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 09/10/2016
 * Time: 17:07
 */

namespace c00\QueryBuilder;

interface IQry {
    public function orderBy($column, $ascending = true);
    public function getSql(&$params = null);
    public function getWhereParams();
    public function getUpdateParams();
    public function getInsertParams();
    public function limit($limit, $offset = 0);
    public function asClass($className);
    public function getClass();
    public function max($column);
    public function selectFunction($function, $column, $alias = null);
    public function from($tables);
    public function join($table, $column1, $operator, $column2);
    public function outerJoin($table, $column1, $operator, $column2, $direction = "LEFT");
    public function where($condition1, $operator, $condition2);
    public function whereIn($column, array $values);
    public function whereCount();
    public function checkDataType($object, $allowedTypes);

}