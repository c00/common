<?php

namespace c00\QueryBuilder\components;

use c00\common\Helper;
use c00\QueryBuilder\QryHelper;

class JoinClass extends Join
{
    public $class;


    public static function newJoinClass($class, $table, $alias, $condition1, $operator, $condition2) {
        $j = new JoinClass();
        $j->table = $table;
        $j->on = Where::new($condition1, $operator, '**'.$condition2, Where::TYPE_JOIN);
        $j->on->isFirst = false;
        $j->alias = $alias;
        $j->class = $class;

        return $j;
    }

    public static function newOuterJoinClass($class, $table, $alias, $condition1, $operator, $condition2, $direction = "LEFT") {
        $j = new JoinClass();
        $j->table = $table;
        $j->on = Where::new($condition1, $operator, '**'.$condition2, Where::TYPE_JOIN);
        $j->on->isFirst = false;
        $j->alias = $alias;
        $j->isOuter = true;
        $j->direction = $direction;
        $j->class = $class;

        return $j;
    }

    public function toString($ps = null)
    {
        $table = QryHelper::encap($this->table);
        $alias = ($this->alias) ? " AS `{$this->alias}`" : "";

        return " {$this->getJoinKeywords()}{$table}{$alias}{$this->on->toString($ps)}";
    }

    public function getClassColumns() {
        return Helper::getClassColumns($this->class, $this->alias);
    }
}