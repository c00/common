<?php

namespace c00\QueryBuilder\components;


use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\QryHelper;

class OrderBy implements IQryComponent
{
    public $column;
    public $ascending = true;
    private $addition = '';

    public function toString($ps = null)
    {
        if (!$this->column) return '';

        $string =  QryHelper::encap($this->column);

        if (!empty($this->addition)) $string.= ' ' . $this->addition;

        $string .= ($this->ascending) ? " ASC" : " DESC";

        return $string;
    }

    public static function new($column, $ascending = true, $addition = '') {
        $c = new static();
        $c->column = $column;
        $c->ascending = $ascending;
        $c->addition = $addition;

        return $c;
    }


}