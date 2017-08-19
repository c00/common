<?php

namespace c00\QueryBuilder\components;

use c00\QueryBuilder\QryHelper;

class Join extends From
{
    /** @var Comparison */
    private $on;

    private $isOuter;
    private $direction;


    public static function newJoin($table, $alias, $condition1, $operator, $condition2) {
        $j = new Join();
        $j->table = $table;
        $j->on = Where::new($condition1, $operator, '**'.$condition2, Where::TYPE_JOIN);
        $j->on->isFirst = false;
        $j->alias = $alias;

        return $j;
    }

    public static function newOuterJoin($table, $alias, $condition1, $operator, $condition2, $direction = "LEFT") {
        $j = new Join();
        $j->table = $table;
        $j->on = Where::new($condition1, $operator, '**'.$condition2, Where::TYPE_JOIN);
        $j->on->isFirst = false;
        $j->alias = $alias;
        $j->isOuter = true;
        $j->direction = $direction;

        return $j;
    }

    public function toString($ps = null)
    {
        $table = QryHelper::encap($this->table);
        $alias = ($this->alias) ? " AS `{$this->alias}`" : "";

        return " {$this->getJoinKeywords()}{$table}{$alias}{$this->on->toString($ps)}";
    }

    private function getJoinKeywords() {
        if (!$this->isOuter) return "JOIN ";

        return "{$this->direction} OUTER JOIN ";
    }
}