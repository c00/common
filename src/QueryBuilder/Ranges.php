<?php
/**
 * Created by PhpStorm.
 * User: c00yt
 * Date: 04/12/2016
 * Time: 14:19
 */

namespace c00\QueryBuilder;


class Ranges
{
    public $column;
    public $alias;
    public $cases = [];

    public static function newRanges($groupColumn, $alias) : Ranges
    {
        $r = new Ranges();
        $r->column = $groupColumn;
        $r->alias = $alias;

        return $r;
    }

    public function addCaseBetween($label, $low, $high)
    {
        //todo: escaping
        $this->cases[] = "WHEN ". QryHelper::encap($this->column) . " BETWEEN $low AND $high THEN '$label'";
    }

    public function addCaseLessThan($label, $value) {
        $this->cases[] = "WHEN ". QryHelper::encap($this->column) . " < $value THEN '$label'";
    }

    public function addCaseGreaterThan($label, $value) {
        $this->cases[] = "WHEN ". QryHelper::encap($this->column) . " > $value THEN '$label'";
    }

    public function getCaseColumn() : string
    {
        return "CASE " . implode(' ', $this->cases) . " END AS " . $this->alias;
    }

}