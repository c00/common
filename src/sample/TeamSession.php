<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 23/08/17
 * Time: 14:16
 */

namespace c00\sample;


use c00\common\AbstractDatabaseObject;
use c00\common\CovleDate;

class TeamSession extends AbstractDatabaseObject
{
    public $token;
    /** @var CovleDate */
    public $created;
    /** @var CovleDate */
    public $expires;
    /** @var CovleDate */
    public $renewed;
    public $teamId;
    public $deviceId;
    public $deviceType;

    protected $_dataTypes = [
        'created' => CovleDate::class,
        'expires' => CovleDate::class,
        'renewed' => CovleDate::class,
        'teamId' => 'int'
    ];

    protected $_identifier = 'token';


}