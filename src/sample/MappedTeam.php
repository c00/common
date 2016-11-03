<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 00:58
 */

namespace c00\sample;

use c00\common\AbstractDatabaseObject;
use c00\common\CovleDate;
use c00\common\Helper as H;

class MappedTeam extends AbstractDatabaseObject
{
    public $id;
    public $name;
    public $code;
    public $active;
    public $image;
    /** @var CovleDate */
    public $created;

    //Map in different ways 1
    protected $_mapping = ['id' => 'TEAMID'];

    //Set datatypes 1
    protected $_dataTypes = ['id' => 'int'];

    public function __construct()
    {
        //Map in different ways 2
        $this->mapProperty('name', 'TEAMNAME');

        //Map in different ways 3
        $this->mapProperties(
            [
                'code' => 'TEAMCODE',
                'active' => 'TEAMSTATUS'
            ]
        );


        //Set datatypes 2
        $this->setPropertyType('active', 'int');

        //Set datatypes 3
        $this->setPropertyTypes([
            'code' => 'float',
            'created' => CovleDate::class,
            'active' => 'bool'
        ]);

    }
}