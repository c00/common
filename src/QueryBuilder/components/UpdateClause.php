<?php


namespace c00\QueryBuilder\components;


use c00\common\Helper;
use c00\common\IDatabaseObject;
use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\QueryBuilderException;

class UpdateClause extends From
{
    /** @var SetParam[] */
    public $params = [];

    public function toString($ps = null)
    {
        return  'UPDATE ' . parent::toString($ps);
    }

    /**
     * @param $object IDatabaseObject
     * @throws \Exception
     */
    public function setObject($object) {
        if (is_array($object)){
            $array = $object;
        }else {
            $array = $object->toArray();
        }

        if (count($array) == 0){
            throw new \Exception("Nothing to set!");
        }

        $this->params = [];
        foreach ($array as $key => $value) {
            $this->params[] = SetParam::newSetParam($key, $value);
        }
    }

    /**
     * @param ParamStore $ps
     * @return string
     * @throws \Exception
     */
    public function getSetString($ps = null) {
        $strings = [];
        foreach ($this->params as $param) {
            $strings[] = $param->toString($ps);
        }

        return " SET " . implode(', ', $strings);
    }

}