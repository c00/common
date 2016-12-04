<?php
/**
 * Created by PhpStorm.
 * User: c00yt
 * Date: 04/12/2016
 * Time: 14:19
 */

namespace c00\QueryBuilder;


use c00\common\Helper;

class Ranges
{
    public $column;
    public $alias;
    public $cases = [];
    public $params = [];

    public static function newRanges($groupColumn, $alias) : Ranges
    {
        $r = new Ranges();
        $r->column = $groupColumn;
        $r->alias = $alias;

        return $r;
    }

    public function addCaseBetween($label, $low, $high)
    {
        //todo figure out if we should escape labels
        $lowId = Helper::uniqueId();
        $this->params[$lowId] = $low;
        $highId = Helper::uniqueId();
        $this->params[$highId] = $high;
        $this->cases[] = "WHEN ". QryHelper::encap($this->column) . " BETWEEN :$lowId AND :$highId THEN '$label'";
    }

    public function addCaseLessThan($label, $value) {
        $id = Helper::uniqueId();
        $this->params[$id] = $value;
        $this->cases[] = "WHEN ". QryHelper::encap($this->column) . " < :$id THEN '$label'";
    }

    public function addCaseGreaterThan($label, $value) {
        $id = Helper::uniqueId();
        $this->params[$id] = $value;
        $this->cases[] = "WHEN ". QryHelper::encap($this->column) . " > :$id THEN '$label'";
    }

    public function getCaseColumn() : string
    {
        return "CASE " . implode(' ', $this->cases) . " END";
    }

}