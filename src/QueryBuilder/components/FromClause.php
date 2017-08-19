<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 19/08/17
 * Time: 13:09
 */

namespace c00\QueryBuilder\components;


use c00\common\IDatabaseObject;
use c00\QueryBuilder\QueryBuilderException;

class FromClause implements IQryComponent
{
    /** @var From[] */
    public $tables = [];


    /** Will return the FromClass object if there is any.
     * Note there can only be 1 FromClass object per Qry.
     * @return FromClass|null
     */
    public function getTableWithClass(){
        foreach ($this->tables as $f) {
            if ($f instanceof FromClass) return $f;
        }

        return null;
    }

    public function getTableNames() {
        $tables = [];
        foreach ($this->tables as $f) {
            $tables[] = $f->getTableName();
        }

        return $tables;
    }

    public function toString($ps = null)
    {
        if (count($this->tables) === 0) throw new QueryBuilderException("No FROM clause!");


        $tables = [];
        foreach ($this->tables as $f) {
            $tables[] = $f->toString();
        }

        return " FROM " . implode(', ', $tables);
    }


}