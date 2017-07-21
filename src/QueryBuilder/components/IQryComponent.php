<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 21/07/17
 * Time: 11:35
 */

namespace c00\QueryBuilder\components;


use c00\QueryBuilder\ParamStore;

interface IQryComponent
{
    /**
     * @param ParamStore|null $ps Provide the parameter store to generate unique Ids. Optional if no Ids are needed.
     * @return string
     */
    public function toString($ps = null);
}