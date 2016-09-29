<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 03/04/2016
 * Time: 10:23
 */

namespace c00\common;

abstract class AbstractDependencyContainer
{
    protected $dependencies = [];

    function __construct(){
        //Not much of anything.
    }

    public function add($dependency){
        $name = get_class($dependency);
        if (isset($this->dependencies[$name])){
            //Maybe throw an error if you want.
            return;
        }

        //add
        $this->dependencies[$name] = $dependency;

        //Add the dependencycontainer to the dependency if applicable
        $traits = class_uses($dependency);
        if (!in_array(TDependency::class, $traits)){
            return;
        }
        /** @var TDependency $dependency */
        $dependency->setDc($this);
    }

    protected function getDependency($name){
        if (!isset($this->dependencies[$name])){
            return false;
        }

        return $this->dependencies[$name];
    }
}