<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 21/07/17
 * Time: 11:36
 */

namespace c00\QueryBuilder;


use c00\common\Helper;

class ParamStore
{
    private $params = [];

    public function getParams()
    {
        return $this->params;
    }

    public function addParam($value){
        $id = $this->getNewId();
        $this->params[$id] = $value;

        return $id;
    }

    public function addParams($values){
        $keys = [];
        foreach ($values as $value) {
            $keys[] = $this->addParam($value);
        }

        return $keys;
    }

    private function getNewId(){
        $id = Helper::uniqueId();
        $i = 0;

        while (isset($this->params[$id])){
            //Brute force it.
            $id = Helper::uniqueId();
            $i++;
            if ($i > 1000){
                throw new \Exception("Something has gone really wrong trying to generate a random id.");
            }
        }

        return $id;
    }
}