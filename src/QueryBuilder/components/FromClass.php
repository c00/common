<?php


namespace c00\QueryBuilder\components;


use c00\common\Helper;
use c00\QueryBuilder\QueryBuilderException;

class FromClass extends From
{
    public $class;

    public static function newFromClass($class, $table, $alias) {

        /** @var FromClass $f */
        $f = parent::newFrom($table, $alias);
        $f->class = $class;

        return $f;
    }

    /**
     * @return Select[]
     * @throws QueryBuilderException
     */
    public function getClassColumns() {
        return Helper::getClassColumns($this->class, $this->alias);
    }


}