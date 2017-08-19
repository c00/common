<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 17/08/17
 * Time: 16:25
 */

namespace c00\QueryBuilder\components;


use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\QryHelper;

class SelectFunction extends Select
{
    public $keyword;
    public $function;

    public function __construct($function, $column, $alias = null, $keyword = null)
    {
        $this->function = $function;
        $this->column = $column;
        $this->alias = $alias;
        $this->keyword = $keyword;

    }

    public function getColumnName($encapped = true)
    {
        $column = "{$this->function}({$this->column})";

        if ($encapped) return ($this->alias)? "`{$this->alias}`" : QryHelper::encap($column);

        return ($this->alias)? $this->alias : $column;
    }

    public function toString($ps = null)
    {
        $distinctKeyword = ($this->keyword) ? "{$this->keyword} " : "";
        $column = QryHelper::encapStringWithOperators($this->column);
        $alias = ($this->alias) ? " AS `{$this->alias}`" : "";

        return "{$this->function}({$distinctKeyword}{$column}){$alias}";
    }


}