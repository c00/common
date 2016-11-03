<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 03/11/2016
 * Time: 22:16
 */

namespace c00\common;


class Database extends AbstractDatabase
{
    public function __construct($host, $user, $pass, $dbName)
    {
        $this->connect($host, $user, $pass, $dbName);
    }

}