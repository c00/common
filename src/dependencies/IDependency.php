<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 05/10/2016
 * Time: 13:33
 */

namespace c00\dependencies;

interface IDependency
{
    public function setDc(AbstractDependencyContainer $dc);

}
