<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 00:58
 */

namespace c00\sample;

use c00\common\AbstractDatabaseObject;
use c00\common\Helper as H;

class Team extends AbstractDatabaseObject
{
    public $id;
    public $name;
    public $code;
    public $active;
    public $image;

    protected $_dataTypes = [
        'id' => 'int',
        'active' => 'bool'
    ];

    public function __construct()
    {

    }

    public static function newInstance($name, $code, $image)
    {
        $team = new Team();
        $team->name = $name;
        $team->code = $code;
        $team->image = $image;

        return $team;
    }

    /**
     * @param $array
     * @return bool|Team
     */
    public static function fromArray($array)
    {
        /** @var Team $t */
        $t = H::objectFromArray($array, self::class);

        $t->id = (int) isset($array['id']) ? $array['id'] : null;
        //$t->active = (bool) isset($array['active']) ? $array['active'] : null;
        if (isset($array['active'])) $t->active = (bool) $array['active'];

        return $t;
    }
}