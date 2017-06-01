<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 01/06/17
 * Time: 22:08
 */

namespace c00\QueryBuilder;

use c00\common\Helper as H;

class WhereGroup
{
    public $conditions = [];
    public $type = Comparison::TYPE_AND;

    public function __construct()
    {

    }

    public function setUniqueIds(&$params){
        foreach ($this->conditions as &$comparison) {
            if ($comparison->needsUniqueId()) {
                $comparison->uniqueId = H::getUniqueId($params);
                $params[$comparison->uniqueId] = $comparison->condition2;
            }
        }
    }

    /**
     * @param $condition1 string
     * @param $operator string
     * @param $condition2 string
     * @param string $type string
     * @return WhereGroup
     */
    public static function new($condition1, $operator, $condition2, $type = Comparison::TYPE_AND)
    {
        $g = new WhereGroup();

        $g->conditions[] = new Comparison($condition1, $operator, $condition2);;
        $g->type = $type;

        return $g;
    }

    /**
     * @param $condition1 string
     * @param $operator string
     * @param $condition2 string
     * @return WhereGroup string
     */
    public function where($condition1, $operator, $condition2)
    {
        $this->conditions[] = new Comparison($condition1, $operator, $condition2);;
        return $this;
    }

    /**
     * @param $condition1 string
     * @param $operator string
     * @param $condition2 string
     * @return WhereGroup string
     */
    public function orWhere($condition1, $operator, $condition2)
    {
        $this->conditions[] = new Comparison($condition1, $operator, $condition2, Comparison::TYPE_OR);

        return $this;
    }

    public function toString(){
        if (count($this->conditions) === 0) return "";

        $isFirst = true;
        $string = "(";
        foreach ($this->conditions as $comparison) {
            if ($isFirst){
                $isFirst = false;
            } else if ($comparison->type === Comparison::TYPE_AND) {
                $string .= " AND ";
            } else if ($comparison->type === Comparison::TYPE_OR) {
                $string .= " OR ";
            } else {
                throw new QueryBuilderException("No comparison type");
            }

            $string .= $comparison->toString();
        }

        $string .= ")";

        return $string;
    }
}