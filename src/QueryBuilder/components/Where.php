<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 01/06/17
 * Time: 16:15
 */

namespace c00\QueryBuilder\components;

use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\QryHelper;
use c00\QueryBuilder\QueryBuilderException;

/**
 * Class _Comparison
 * @package c00\QueryBuilder
 */
class Where extends Comparison
{
    public $condition1;
    public $operator;
    public $condition2;
    private $id;

    public function __construct()
    {

    }

    /**
     * @param $condition1
     * @param $operator
     * @param $condition2
     * @param string $type
     * @return Where
     */
    public static function new($condition1, $operator, $condition2, $type = self::TYPE_AND){
        $w = new Where();
        $w->condition1 = $condition1;
        $w->condition2 = $condition2;
        $w->operator = $operator;
        $w->type = $type;

        return $w;
    }

    /**
     * @param null|ParamStore $ps
     * @return string
     * @throws QueryBuilderException When the param store is missing and it's needed.
     */
    public function toString($ps = null){
        $condition1 = QryHelper::encap($this->condition1);

        //allow IS NULL and IS NOT NULL
        if ($this->condition2 === null){
            if ($this->operator === '=') $this->operator = 'IS';
            if ($this->operator === '!=') $this->operator = 'IS NOT';

            return " {$this->getType()} {$condition1} {$this->operator} NULL";
        }

        //Don't escape condition 2 starting with **
        if (!$this->shouldEscape()){
            //strip ** and encapsulate
            $condition2 = QryHelper::encap(substr($this->condition2, 2));
            return " {$this->getType()} {$condition1} {$this->operator} {$condition2}";
        }

        if ($ps === null && $this->id === null) throw new QueryBuilderException("No ParamStore.");
        //Reuse the ID if it was already generated.

        if (!$this->id) $this->id = $ps->addParam($this->condition2);

        return " {$this->getType()} {$condition1} {$this->operator} :{$this->id}";
    }

    private function shouldEscape(){

        if (substr($this->condition2, 0, 2) === '**') return false;

        return true;
    }

}