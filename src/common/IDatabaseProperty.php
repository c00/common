<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 03/04/2016
 * Time: 09:49
 */
namespace c00\common;

interface IDatabaseProperty{

    /** Turns a database value (column) into an object.
     * @param string|int $value The value from the database
     * @return IDatabaseProperty
     */
    public static function fromDb($value);

    /** Converts the object into a value the Database will accept.
     * @return string|int
     */
    public function toDb();
}