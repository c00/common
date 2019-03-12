<?php

namespace c00\common;


class Database extends AbstractDatabase
{
    public function __construct($host, $user, $pass, $dbName)
    {
        $this->connect($host, $user, $pass, $dbName);
    }

}