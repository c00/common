<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 00:58
 */

namespace c00\sample;

use c00\common\Helper as H;
use c00\common\IDatabaseObject;

class Team implements IDatabaseObject
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
        $t = H::objectFromArray($array, self::class);

        return $t;
    }

    /** Converts the object into an array
     * @return array
     */
    public function toArray()
    {
        $array = H::objectToArray($this);

        $array['code'] = strtolower($this->code);
        unset($array['color']);

        return $array;
    }

    public function toShowable(){
        $a = H::objectToArray($this);
        $a['active'] = (bool) $this->active;
        $a['id'] = (int) $this->id;

        return $a;
    }
}