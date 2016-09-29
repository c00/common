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
     * @return array
     */
    public function toArray();

    public function toShowable();
}