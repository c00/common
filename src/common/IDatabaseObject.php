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

    //todo: rename this toDb() on version 1.0
    /** Converts the object into an array
     * @param $keepNulls bool Keep properties with null values
     * @param $keepNested bool Determines whether to call toArray() on properties that are also IDatabaseObjects, or ignore them.
     * @return array
     */
    public function toArray($keepNulls = false, $keepNested = true);

    public function toShowable();

    /** return the ID of this object. */
    public function getIdentifier();
}