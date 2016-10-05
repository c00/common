<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 05/10/2016
 * Time: 13:53
 */

namespace c00\sample;


use c00\dependencies\AbstractDependencyContainer;

class DependencyContainer extends AbstractDependencyContainer
{
    public function getDependency($name)
    {
        return parent::getDependency($name);
    }

}