<?php

namespace c00\QueryBuilder\components;

use c00\common\Helper as H;
use c00\QueryBuilder\QueryBuilderException;

class SelectClause implements IQryComponent
{
    /** @var Select[] */
    private $columns = [];
    public $distinct = false;

    public function getColumns() {
        return $this->columns;
    }

    /**
     * @param $column string The column
     * @param $alias string The alias
     * @throws QueryBuilderException when duplicate column names are used.
     */
    public function addColumn($column, $alias = null) {
        $select = Select::new($column, $alias);

        $this->addSelect($select);
    }

    /**
     * @param $select Select
     * @throws QueryBuilderException when duplicate column names are used.
     */
    public function addSelect($select) {
        $name = $select->getColumnName(false);

        if (isset($this->columns[$name])) throw new QueryBuilderException("Duplicate column name: $name");

        $this->columns[$select->getColumnName(false)] = $select;
    }

    /**
     * @param $columns array as alias => column
     */
    public function addColumns($columns) {
        if (is_string($columns)) $columns = [$columns];

        foreach ($columns as $index => $column) {
            $alias = (is_numeric($index)) ? null : $index;

            $this->addColumn($column, $alias);
        }
    }

    /**
     * @param $fromClass FromClass
     * @param $joinClasses JoinClass[]
     */
    public function addClassColumns($fromClass, $joinClasses) {
        /** @var FromClass[] $array */
        $array = $joinClasses;
        if ($fromClass) $array[] = $fromClass;

        if (count($array) === 0) return;

        /** @var Select[] $columns */
        $columns = [];

        if ($this->hasNoColumns()){
            //No columns yet, or 1 column that's '*', replace with all class columns
            $this->columns = [];
            foreach ($array as $f) {
                $columns = array_merge($columns, $f->getClassColumns());
            }
        } else {
            /** @var FromClass[] $tables */
            $tables = H::arrayOfObjectsToAssocArray($array, 'alias');
            foreach ($this->columns as $index => $select) {
                //Only take things that end in .*
                if (preg_match('/\.\*$/', $select->column) == 0) continue;

                $alias = substr($select->column, 0, strlen($select->column) -2);
                if (isset($tables[$alias])) {
                    //remove this [alias].*
                    unset($this->columns[$index]);
                    $columns = array_merge($columns, $tables[$alias]->getClassColumns());
                }

            }
        }

        //Set columns
        $this->columns = array_unique(array_merge($this->columns, $columns));
    }

    private function setStarForColumns() {
        if (count($this->columns) === 0) $this->columns[] = Select::new('*');
    }

    /** Check to see if there are any columns (other than * )
     * @return bool
     */
    private function hasNoColumns() {
        if (count($this->columns) === 0) return true;


        if (count($this->columns) === 1 && isset($this->columns['*'])) {
            return true;
        }

        return false;
    }

    public function toString($ps = null)
    {
        $this->setStarForColumns();

        $columns = [];
        foreach ($this->columns as $f) {
            $columns[] = $f->toString($ps);
        }

        $distinct = ($this->distinct) ? 'DISTINCT ' : "";

        return "SELECT $distinct" . implode(', ', $columns);
    }

}