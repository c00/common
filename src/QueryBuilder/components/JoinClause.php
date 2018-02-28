<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 19/08/17
 * Time: 13:09
 */

namespace c00\QueryBuilder\components;


use c00\common\IDatabaseObject;
use c00\QueryBuilder\QueryBuilderException;

class JoinClause implements IQryComponent
{
    /** @var Join[] */
    public $joins = [];


    /** Will return the JoinClass objects if there is any.
     * @return JoinClass[]
     */
    public function getJoinsWithClass(){
        $result = [];
        foreach ($this->joins as $j) {
            if ($j instanceof JoinClass) $result[] = $j;
        }

        return $result;
    }

    public function hasAny() {
        return count($this->joins) > 0;
    }

    public function toString($ps = null)
    {
        if (count($this->joins) === 0) return "";

        $string = "";
        foreach ($this->joins as $j) {
            $string .= $j->toString($ps);
        }

        return $string;
    }


}