<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 01/06/17
 * Time: 16:15
 */

namespace c00\QueryBuilder;


class Comparison
{
    const TYPE_AND = 'AND';
    const TYPE_OR = 'OR';

    public $condition1;
    public $operator;
    public $condition2;
    public $type;
    public $shouldEscape;
    public $uniqueId;

    public function __construct($condition1, $operator, $condition2, $type = self::TYPE_AND)
    {
        $this->condition1 = $condition1;
        $this->condition2 = $condition2;
        $this->operator = $operator;
        $this->type = $type;

        $this->checkEscape();
    }

    public function toString(){
        $con1 = QryHelper::encap($this->condition1);

        //allow IS NULL and IS NOT NULL
        if ($this->condition2 === null){
            if ($this->operator === '=') $this->operator = 'IS';

            return "{$con1} {$this->operator} NULL";
        }

        //Don't escape condition 2 starting with **
        if (!$this->shouldEscape){
            $con2 = QryHelper::encap($this->condition2);
            return "{$con1} {$this->operator} {$con2}";
        }

        if (!$this->uniqueId) throw new QueryBuilderException("No unique ID for parameter");

        return "{$con1} {$this->operator} :{$this->uniqueId}";
    }

    private function checkEscape(){
        $check = $this->condition2;
        if (substr($check, 0, 2) === '**'){
            $this->condition2 = substr($check, 2);
            $this->shouldEscape = false;
            return;
        }

        $this->shouldEscape = true;
    }

    public function needsUniqueId(){
        return ($this->shouldEscape && $this->condition2 !== null);

    }
}