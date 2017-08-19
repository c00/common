<?php
/**
 * Created by PhpStorm.
 * User: c00yt
 * Date: 04/12/2016
 * Time: 14:19
 */

namespace c00\QueryBuilder\components;


use c00\common\Helper;
use c00\QueryBuilder\components\Select;
use c00\QueryBuilder\QueryBuilderException;

class Ranges extends Select
{
    /** @var RangeCase[] */
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
        $this->cases[] = RangeCase::between($this->column, $label, $low, $high);
    }

    public function addCaseLessThan($label, $value) {
        $this->cases[] = RangeCase::lessThan($this->column, $label, $value);
    }

    public function addCaseGreaterThan($label, $value) {
        $this->cases[] = RangeCase::greaterThan($this->column, $label, $value);
    }

    /**
     * @deprecated use toString() instead
     * @throws QueryBuilderException
     */
    public function getCaseColumn() : string
    {
        throw new QueryBuilderException("Deprecated. use ToString() instead");
    }

    public function toString($ps = null)
    {
        $cases = [];
        foreach ($this->cases as $case) {
            $cases[] = $case->toString($ps);
        }
        return "CASE " . implode(' ', $cases) . " END AS `{$this->alias}`";

    }

}