<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 03/04/2016
 * Time: 09:49
 */
namespace c00\common;

interface IDatabaseObject{

    /** Returns an object
     * @param $array array
     * @return IDatabaseObject
     */
    public static function fromArray($array);

    /** Converts the object into an array
     * @param $keepNulls bool Keep properties with null values
     * @return array
     */
    public function toArray($keepNulls = false);

    public function toShowable();
}