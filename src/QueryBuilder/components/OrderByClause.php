<?php

namespace c00\QueryBuilder\components;

use c00\common\Helper as H;
use c00\QueryBuilder\QueryBuilderException;

class OrderByClause implements IQryComponent
{
    /** @var OrderBy[] */
    private $columns = [];


    /**
     * @param $column string
     * @param bool $ascending
     * @param string $addition e.g. IS NULL
     * @return $this
     */
    public function addColumn($column, $ascending = true, $addition = '') {
        $col = OrderBy::new($column, $ascending, $addition);

        $this->add($col);
        return $this;
    }

    /**
     * @param $orderBy OrderBy
     * @return $this
     */
    private function add($orderBy) {
        $this->columns[] = $orderBy;
        return $this;
    }

    public function toString($ps = null)
    {
        if (empty($this->columns)) return '';

        $columns = [];
        foreach ($this->columns as $column) {
            $columns[] = $column->toString();
        }

        $colString = implode(', ', $columns);
        if (empty($colString)) return '';

        return " ORDER BY $colString";
    }

}