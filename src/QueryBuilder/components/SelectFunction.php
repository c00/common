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

class SelectFunction implements IQryComponent
{
    public $column;
    public $alias;
    public $keyword;
    public $function;

    public function __construct($function, $column, $alias = "", $keyword = null)
    {
        $this->function = $function;
        $this->column = $column;
        $this->alias = $alias;
        $this->keyword = $keyword;

    }

    public function toString($ps = null)
    {
        $distinctKeyword = ($this->keyword) ? "{$this->keyword} " : "";
        $column = QryHelper::encapStringWithOperators($this->column);
        $alias = ($this->alias) ? " AS `{$this->alias}`" : "";

        return "{$this->function}({$distinctKeyword}{$column}){$alias}";
    }


}