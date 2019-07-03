<?php

namespace c00\QueryBuilder\components;

use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\Qry;
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
     * @param $group WhereGroup
     * @param string $type
     *
     * @return WhereGroup
     */
    public static function newGroup($group, $type = Comparison::TYPE_AND) {
        $g = new WhereGroup();

        $g->whereGroup($group);
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
     * @param $group WhereGroup
     * @return WhereGroup
     */
    public function whereGroup($group){
        $this->conditions[] = $group;
        return $this;
    }

    /**
     * @param $group WhereGroup
     * @return WhereGroup
     */
    public function orWhereGroup($group){
        $group->type = Comparison::TYPE_OR;
        $this->conditions[] = $group;
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