<?php

namespace c00\QueryBuilder\components;

use c00\QueryBuilder\QryHelper;

class Join extends From
{
    /** @var Comparison []*/
    protected $on = [];

    protected $isOuter;
    protected $direction;


    public static function newJoin($table, $alias, $condition1, $operator, $condition2) {
        $j = new Join();
        $j->table = $table;
        $w = Where::new($condition1, $operator, '**'.$condition2, Where::TYPE_JOIN);
        $w->isFirst = false;
        $j->on[] = $w;
        $j->alias = $alias;

        return $j;
    }

    public static function newOuterJoin($table, $alias, $condition1, $operator, $condition2, $direction = "LEFT") {
        $j = new Join();
        $j->table = $table;
        $w = Where::new($condition1, $operator, '**'.$condition2, Where::TYPE_JOIN);
        $w->isFirst = false;
        $j->on[] = $w;
        $j->alias = $alias;
        $j->isOuter = true;
        $j->direction = $direction;

        return $j;
    }

	/**
	 * @param $condition1 string Column for condition 1
	 * @param $operator string
	 * @param $condition2 string Column or value for condition 2
	 * @param bool $onColumn True if $condition2 is a columns, false if it is a value
	 *
	 * @return $this
	 */
    public function andOn($condition1, $operator, $condition2, $onColumn = true){
    	$prefix = ($onColumn) ? '**' : '';

        $this->on[] = Where::new($condition1, $operator, $prefix.$condition2, Where::TYPE_AND);
        return $this;
    }

	/**
	 * @param $condition1 string Column for condition 1
	 * @param $operator string
	 * @param $condition2 string Column or value for condition 2
	 * @param bool $onColumn True if $condition2 is a columns, false if it is a value
	 *
	 * @return $this
	 */
    public function orOn($condition1, $operator, $condition2, $onColumn = true){
	    $prefix = ($onColumn) ? '**' : '';

        $this->on[] = Where::new($condition1, $operator, $prefix.$condition2, Where::TYPE_OR);
        return $this;
    }

    private function morOn($condition1, $operator, $condition2){
        throw new \Exception("Hehehe. Moron.");
    }


    protected function getOnString($ps) {
        $string = '';
        foreach ($this->on as $comparison) {
            $string .= $comparison->toString($ps);
        }
        return $string;
    }

    public function toString($ps = null)
    {
        $table = QryHelper::encap($this->table);
        $alias = ($this->alias) ? " AS `{$this->alias}`" : "";

        return " {$this->getJoinKeywords()}{$table}{$alias}{$this->getOnString($ps)}";
    }

    protected function getJoinKeywords() {
        if (!$this->isOuter) return "JOIN ";

        return "{$this->direction} OUTER JOIN ";
    }
}