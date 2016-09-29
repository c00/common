<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 29/09/2016
 * Time: 18:55
 */

namespace c00\common;

use c00\common\Helper as H;

abstract class AbstractDatabaseObject implements IDatabaseObject
{

    /** Turns an array into an instance of @static
     * @param $array array
     * @return static
     */
    public static function fromArray($array)
    {
        $o = new static;

        $t = H::objectFromArray($array, $o);
        if ($t instanceof self){
            return $t;
        }

        return null;
    }

    /** Converts the object into an array.
     * Use this to 'prepare' an object to go into the database. Override this function to do any transformations.
     * @return array
     */
    public function toArray()
    {
        $array = H::objectToArray($this);

        return $array;
    }

    /** Converts the object into an array that can be passed on to a client.
     * Use this to 'prepare' an object to go to a client. Override thid function to do any transformations.
     * @return array
     */
    public function toShowable()
    {
        $array = H::objectToArray($this);

        return $array;
    }

}