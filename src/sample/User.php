<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 00:58
 */

namespace c00\sample;

use c00\common\AbstractDatabaseObject;

class User extends AbstractDatabaseObject
{
    public $id;
    public $name;
    public $email;
    public $active;
    public $profileImage;

    public function __construct()
    {

    }

}