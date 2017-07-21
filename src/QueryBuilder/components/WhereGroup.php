<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 01/06/17
 * Time: 22:08
 */

namespace c00\QueryBuilder\components;

use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\QueryBuilderException;

class WhereGroup extends Comparison
{
    /** @var Comparison[] */
    public $conditions = [];

    public function __construct()
    {

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

        $g->conditions[] = Where::new($condition1, $operator, $condition2);
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
        $this->conditions[] = Where::new($condition1, $operator, $condition2);;
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
        $this->conditions[] = Where::new($condition1, $operator, $condition2, Comparison::TYPE_OR);

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return WhereGroup
     */
    public function whereIn($column, array $values){

        $wi = WhereIn::new($column, $values);

        $this->conditions[] = $wi;

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return WhereGroup
     */
    public function whereNotIn($column, array $values){

        $wi = WhereIn::new($column, $values);
        $wi->isNotIn = true;

        $this->conditions[] = $wi;

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return WhereGroup
     */
    public function orWhereIn($column, array $values){

        $wi = WhereIn::new($column, $values, Comparison::TYPE_OR);


        $this->conditions[] = $wi;

        return $this;
    }

    /**
     * @param $column
     * @param array $values
     * @return WhereGroup
     */
    public function orWhereNotIn($column, array $values){

        $wi = WhereIn::new($column, $values, Comparison::TYPE_OR);
        $wi->isNotIn = true;

        $this->conditions[] = $wi;

        return $this;
    }

    /**
     * @param null|ParamStore $ps
     * @return string
     * @throws QueryBuilderException
     */
    public function toString($ps = null){

        if (count($this->conditions) === 0) throw new QueryBuilderException("No conditions in WhereGroup");

        $string = "";
        $first = true;

        foreach ($this->conditions as $condition) {
            if ($first){
                $condition->isFirst = true;
                //A bit hacky way to strip WHERE
                $string .= str_replace(" WHERE ", "", $condition->toString($ps));

                $first = false;
            } else {
                $condition->isFirst = false;
                $string .= $condition->toString($ps);
            }

        }

        return " {$this->getType()} ($string)";
    }
}