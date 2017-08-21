<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 20/08/17
 * Time: 11:29
 */

namespace c00\sample;


use c00\common\AbstractDatabaseObject;
use c00\common\CovleDate;

class Session extends AbstractDatabaseObject
{
    public $id;
    public $userId;
    public $token;
    public $expires;

    protected $_dataTypes = [
        'id' => 'int',
        'userId' => 'int',
        'expires' => CovleDate::class
    ];

}