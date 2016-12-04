<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 31/03/2016
 * Time: 01:10
 */

namespace c00\common;
use c00\dmc\DependencyContainer;
use c00\dmc\Challenge;
use c00\dmc\Answer;
use c00\dmc\Team;

/**
 * Class Helper
 * @package c00\common
 */
class Helper
{
    public static function getArrayValue($array, $value, $default = ""){
        if (!is_array($array)) return $default;

        if (!isset($array[$value])) return $default;

        return $array[$value];
    }

    /**
     * Checks if an array has a list of properties set.
     *
     * @param array $a the array to check.
     * @param array $properties The list of properties it should have.
     * @return bool True if okay, False otherwise.
     */
    static function hasProperties($a, array $properties){
        if (!is_array($a)) return false;

        foreach ($properties as $p){
            if (!isset($a[$p])) return false;
        }

        return true;
    }

    /**
     * Copies a property from an array into an object (if it exists in the array).
     *
     * @param $from array The array with properties to be copied.
     * @param $to object The object with, by reference.
     * @param $property string the name of the property to be copied.
     * @return bool False if array didn't have the property. Otherwise true.
     */
    public static function copyArrayPropertyToObject(array $from, &$to, $property){
        //This copies the property of one array into the other array.
        if (!isset($from[$property])){
            return false;
        }

        $to->$property = $from[$property];
        return true;
    }

    /**
     * Copies properties from an array into an object (if it exists in the array).
     *
     * Properties will be gotten from class definition.
     *
     * @param $from array The array with properties to be copied.
     * @param $to object The object with, by reference..
     * @return bool False if array didn't have the property. Otherwise true.
     */
    public static function copyArrayPropertiesToObject(array $from, &$to){
        $result = true;

        $class_vars = get_class_vars(get_class($to));

        foreach ($class_vars as $name => $value) {
            if (!self::copyArrayPropertyToObject($from, $to, $name)) $result = false;
        }

        return $result;
    }

    /**
     * Will convert an object into an array.
     *
     * In the process it will clean out anything that's not defined in the class definition.
     * @param $object object Any object you want converted.
     * @param $keepNulls bool Defines if null values will be removed from the result. Defaults to true.
     * @return array
     */
    public static function objectToArray($object, $keepNulls = false){
        $result = [];
        foreach(get_class_vars(get_class($object)) as $key => $value){
            if((!isset($object->$key) || $object->$key === null) && !$keepNulls) {
                //Go to the next one.
                continue;
            }

            //toShowable on other objects as well.
            if (is_object($object->$key) && $object->$key instanceof IDatabaseObject){
                $result[$key] =$object->$key->toShowable();
            }else {
                $result[$key] = $object->$key;
            }


        }
        return $result;
    }

    /** Return an array of IDatabaseObjects as Showable arrays.
     * @param array $a The objects in an array
     * @return array The showable array.
     * @throws \Exception when there's no IDatabaseObject interface
     */
    public static function toShowables(array $a){
        $result = [];
        foreach ($a as $object) {
            if (is_object($object) && $object instanceof IDatabaseObject){
                $result[] = $object->toShowable();
            } else {
                throw new \Exception("I need IDatabaseObjects!");
            }

        }

        return $result;
    }

    /** Transform an array of objects into an associative array of objects.
     *
     * @param array $array The array of objects
     * @param $assocKey string The property to use as key
     * @param bool $ignoreMissingProperty Ignore objects that don't have the $assocKey property. Will throw exceptions on false.
     * @throws \Exception When array doesn't have objects, or when key is not found in Object
     * @return array The resulting assoc array of objects.
     */
    public static function arrayOfObjectsToAssocArray(array $array, $assocKey, $ignoreMissingProperty = true){
        $result = [];

        foreach ($array as $object) {
            if (!is_object($object)){
                throw new \Exception("Not an object.");
            }
            if (!isset($object->$assocKey) && $ignoreMissingProperty){
                continue;
            } elseif (!isset($object->$assocKey) && !$ignoreMissingProperty){
                throw new \Exception("Missing property $assocKey in Object.");
            }

            $result[$object->$assocKey] = $object;
        }

        return $result;
    }

    /** Attempts to convert an array into an object.
     * @param $array array The array to convert
     * @param $type string|object The class name or an instance of the class to convert to
     * @param array $mapping Optional - The mapping used for conversion
     * @return bool|object The object, or false on failure.
     */
    public static function objectFromArray($array, $type, $mapping = []){
        $className = (is_object($type)) ? get_class($type) : $type;

        if (!is_array($mapping)) $mapping = [];
        
        if (!class_exists($className)) return false;


        $object = new $className();
        if (!is_array($array)) return false;

        $class_vars = get_class_vars($className);
        foreach ($class_vars as $name => $item) {
            //Is there a mapping entry?
            $column = (isset($mapping[$name])) ? $mapping[$name] : $name;

            if (isset($array[$column])) $object->$name = $array[$name];
        }

        return $object;
    }

    /** Return a random string.
     *
     * Uses openssl_pseudo_random_bytes, so it should be sort of crypt-safe....
     *
     * @param int $bytes
     * @return string
     */
    public static function uniqueId($bytes = 8){
        return bin2hex(openssl_random_pseudo_bytes($bytes));
    }
}