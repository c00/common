<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 03/04/2016
 * Time: 10:23
 */

namespace c00\dependencies;

/**
 * Class AbstractDependencyContainer
 * @package c00\dependencies
 */
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
        if (!in_array(TDependency::class, $traits) && !$dependency instanceof IDependency){
            return;
        }
        /** @var TDependency $dependency */
        $dependency->setDc($this);
    }

    /** Get dependency.
     * @param $name string The classname of the dependency
     * @return mixed|null Will return the Depedency, or null  if it's not found.
     */
    protected function getDependency($name){
        if (!isset($this->dependencies[$name])){
            return null;
        }

        return $this->dependencies[$name];
    }
}