<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 21/07/17
 * Time: 11:44
 */

namespace c00\QueryBuilder\components;

use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\QryHelper;
use c00\QueryBuilder\QueryBuilderException;

class WhereIn extends Comparison
{
    public $column;
    public $values = [];
    public $isNotIn = false;

    public $ids = [];

    /**
     * @param $column
     * @param $values
     * @param string $type Defaults to AND
     * @param $isFirst bool Is this the first comparison of the clause?
     * @return WhereIn
     * @throws QueryBuilderException When no values are given.
     */
    public static function new($column, $values, $type = self::TYPE_AND, $isFirst = false){
        $wi = new WhereIn();

        //if (count($values) === 0) throw new QueryBuilderException("No values given");

        $wi->column = $column;
        $wi->values = $values;
        $wi->type = $type;
        $wi->isFirst = $isFirst;

        return $wi;
    }

    private function getComparator(){
        return $this->isNotIn ? "NOT IN" : "IN";
    }

    /**
     * @param ParamStore|null $ps
     * @throws QueryBuilderException
     * @return string
     */
    public function toString($ps = null) {

        if (count($this->values) === 0) return "";

        if ($ps === null) throw new QueryBuilderException("No ParamStore");

        //encap column
        $column = QryHelper::encap($this->column);

        //Return empty IN clause
        if (count($this->values) === 0) return " {$this->getType()} {$column} {$this->getComparator()} ()";

        if (count($this->ids) === 0) {
            //Create parameters
            $this->ids = $ps->addParams($this->values);
        }

        $inString = ":" . implode(', :', $this->ids);
        return " {$this->getType()} {$column} {$this->getComparator()} ($inString)";
    }
}