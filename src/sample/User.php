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
    public $notADatabaseField;

    /** @var Session */
    public $session;

    protected $_ignore = ['notADatabaseField'];

    //Session should/will be ignored on toArray() (INSERTS and UPDATES) because it's not an IDatabaseProperty or Scalar
    protected $_dataTypes = ['session' => Session::class];

    public function __construct()
    {

    }

    public static function newInstance($name, $email)
    {
        $user = new User();
        $user->name = $name;
        $user->email = $email;

        return $user;
    }

}