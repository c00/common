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

    public function __construct()
    {

    }

    /**
     * @param $array
     * @return bool|Team
     */
    public static function fromArray($array)
    {
        /** @var Team $t */
        $t = H::objectFromArray($array, self::class);

        $t->id = (int) $array['id'];
        $t->active = (bool) $array['active'];

        return $t;
    }
}