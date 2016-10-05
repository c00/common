<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 05/10/2016
 * Time: 13:39
 */

namespace c00\sample;


use c00\common\AbstractDatabase;
use c00\dependencies\TDependency;

class DatabaseWithTrait extends AbstractDatabase
{
    use TDependency;

    public function __construct(){
        //Great
    }

    public function getDc(){
        return $this->dc;
    }

}