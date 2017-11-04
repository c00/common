<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 4-11-17
 * Time: 16:25
 */

namespace c00\QueryBuilder\components;


use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\QryHelper;

class SetParam implements IQryComponent
{
    public $key;
    public $value;
    private $id;

    /**
     * @param $key
     * @param $value
     * @return SetParam
     */
    public static function newSetParam($key, $value) {
        $p = new SetParam();
        $p->key = $key;
        $p->value = $value;

        return $p;
    }

    /**
     * @param ParamStore $ps
     * @return string
     * @throws \Exception
     */
    public function toString($ps = null)
    {
        if (!$ps) throw new \Exception("No Paramstore!");

        if (!$this->id) {
            $this->id = $ps->addParam($this->value);
        }

        return QryHelper::encap($this->key) . ' = :' . $this->id;
    }


}