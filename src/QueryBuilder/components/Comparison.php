<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 21/07/17
 * Time: 11:53
 */

namespace c00\QueryBuilder\components;


use c00\QueryBuilder\ParamStore;

abstract class Comparison
{
    const TYPE_AND = "AND";
    const TYPE_OR = "OR";
    const TYPE_JOIN = "ON";

    public $type = self::TYPE_AND;
    public $isFirst = false;

    /**
     * @param null|ParamStore $ps
     * @erturn string
     */
    public abstract function toString($ps = null);

    public function getType(){
        if ($this->isFirst) return "WHERE";

        return $this->type;
    }
}