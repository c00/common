<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 19/08/17
 * Time: 12:44
 */

namespace c00\QueryBuilder\components;

use c00\QueryBuilder\QryHelper;

class From implements IQryComponent
{

    public $table;
    public $alias;

    public static function newFrom($table, $alias = null) {
        $f = new static();
        $f->table = $table;
        $f->alias = $alias;

        return $f;
    }

    public function getTableName() {
        return ($this->alias)? "`{$this->alias}`" : QryHelper::encap($this->table);
    }

    public function toString($ps = null)
    {
        $column = QryHelper::encap($this->table);

        $alias = ($this->alias) ? " AS `{$this->alias}`" : "";
        return "{$column}{$alias}";
    }


}