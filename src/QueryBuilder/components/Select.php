<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 19/08/17
 * Time: 13:52
 */

namespace c00\QueryBuilder\components;


use c00\QueryBuilder\QryHelper;

class Select implements IQryComponent
{
    public $column;
    public $alias;

    public static function new($column, $alias = null) {
        $f = new static();
        $f->column = $column;
        $f->alias = $alias;

        return $f;
    }

    public function getColumnName() {
        return ($this->alias)? "`{$this->alias}`" : QryHelper::encap($this->column);
    }

    public function toString($ps = null)
    {
        $column = QryHelper::encap($this->column);

        $alias = ($this->alias) ? " AS `{$this->alias}`" : "";
        return "{$column}{$alias}";
    }

}