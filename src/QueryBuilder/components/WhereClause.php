<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 21/07/17
 * Time: 20:15
 */

namespace c00\QueryBuilder\components;


use c00\QueryBuilder\ParamStore;

class WhereClause implements IQryComponent
{
    /** @var Comparison[] */
    public $conditions = [];

    public function toString($ps = null)
    {
        if (count($this->conditions) === 0) return '';

        $result = "";
        $first = true;
        foreach ($this->conditions as $condition) {
            $condition->isFirst = $first;

            $result .= $condition->toString($ps);

            if ($first) $first = false;
        }

        return $result;
    }

    public function isEmpty(){
        return (count($this->conditions) === 0);
    }


}