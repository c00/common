<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 05/10/2016
 * Time: 13:39
 */

namespace c00\sample;


use c00\common\AbstractDatabase;
use c00\dependencies\AbstractDependencyContainer;
use c00\dependencies\IDependency;

class DatabaseWithInterface extends AbstractDatabase implements IDependency
{
    private $dc;

    public function __construct(){
        //Great
    }

    public function setDc(AbstractDependencyContainer $dc)
    {
        $this->dc = $dc;
    }

    public function getDc(){
        return $this->dc;
    }

}