<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 19/08/17
 * Time: 13:09
 */

namespace c00\QueryBuilder\components;

use c00\common\Helper as H;

class SelectClause implements IQryComponent
{
    /** @var Select[] */
    public $columns = [];

    /**
     * @param $fromClass FromClass
     * @param $joinClasses array todo will be JoinClass[]
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
        // todo What about doubles?
        $this->columns = array_merge($this->columns, $columns);
    }

    private function setStarForColumns() {
        if (count($this->columns) === 0) $this->columns[] = Select::new('*');
    }

    /** Check to see if there are any columns (other than * )
     * @return bool
     */
    private function hasNoColumns() {
        if (count($this->columns) === 0) return true;


        if (count($this->columns) === 1
            && isset($this->columns[0])
            && $this->columns[0]->column == '*') {
            return true;
        }

        return false;
    }

    public function toString($ps = null)
    {
        $this->setStarForColumns();

        $columns = [];
        foreach ($this->columns as $f) {
            $columns[] = $f->toString();
        }

        return "SELECT " . implode(', ', $columns);
    }


}