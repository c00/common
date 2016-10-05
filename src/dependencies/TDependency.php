<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 03/04/2016
 * Time: 10:44
 */

namespace c00\dependencies;

trait TDependency
{
    /**
     * @var AbstractDependencyContainer
     */
    protected $dc;

    public function setDc($dc){
        $this->dc = $dc;
    }
}