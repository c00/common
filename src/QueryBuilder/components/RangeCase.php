<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 19/08/17
 * Time: 17:36
 */

namespace c00\QueryBuilder\components;


use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\QryHelper;
use c00\QueryBuilder\QueryBuilderException;

class RangeCase implements IQryComponent
{
    const TYPE_LESS_THAN = 'less';
    const TYPE_GREATER_THAN = 'greater';
    const TYPE_BETWEEN = 'between';

    private $low;
    private $lowId;
    private $high;
    private $highId;
    private $type;
    private $column;
    private $label;


    public function __construct()
    {
    }

    public static function between($column, $label, $low, $high) {
        $rc = new RangeCase();
        $rc->low = $low;
        $rc->high = $high;
        $rc->type = self::TYPE_BETWEEN;
        $rc->column = $column;
        $rc->label = $label;

        return $rc;
    }

    public static function lessThan($column, $label, $value) {
        $rc = new RangeCase();
        $rc->low = $value;
        $rc->type = self::TYPE_LESS_THAN;
        $rc->column = $column;
        $rc->label = $label;

        return $rc;
    }

    public static function greaterThan($column, $label, $value) {
        $rc = new RangeCase();
        $rc->low = $value;
        $rc->type = self::TYPE_GREATER_THAN;
        $rc->column = $column;
        $rc->label = $label;

        return $rc;
    }

    /**
     * @param ParamStore $ps
     * @return string
     * @throws QueryBuilderException when type is unknown
     */
    public function toString($ps = null)
    {
        if ($this->type== self::TYPE_BETWEEN) return $this->toBetweenString($ps);

        if ($this->type== self::TYPE_LESS_THAN) return $this->toLessThanString($ps);

        if ($this->type== self::TYPE_GREATER_THAN) return $this->toGreaterThanString($ps);

        throw new QueryBuilderException("Unknown type: {$this->type}");
    }

    /**
     * @param ParamStore $ps
     * @return string
     * @throws QueryBuilderException when type is unknown
     */
    private function toBetweenString($ps = null) {
        if (!$this->lowId) $this->lowId = $ps->addParam($this->low);
        if (!$this->highId) $this->highId = $ps->addParam($this->high);

        return "WHEN ". QryHelper::encap($this->column) . " BETWEEN :{$this->lowId} AND :{$this->highId} THEN '{$this->label}'";
    }


    /**
     * @param ParamStore $ps
     * @return string
     * @throws QueryBuilderException when type is unknown
     */
    private function toLessThanString($ps = null) {
        if (!$this->lowId) $this->lowId = $ps->addParam($this->low);

        return "WHEN ". QryHelper::encap($this->column) . " < :{$this->lowId} THEN '{$this->label}'";
    }

    /**
     * @param ParamStore $ps
     * @return string
     * @throws QueryBuilderException when type is unknown
     */
    private function toGreaterThanString($ps = null) {
        if (!$this->lowId) $this->lowId = $ps->addParam($this->low);

        return "WHEN ". QryHelper::encap($this->column) . " > :{$this->lowId} THEN '{$this->label}'";
    }

}