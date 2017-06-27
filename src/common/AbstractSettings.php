<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 30/03/2016
 * Time: 23:08
 */

namespace c00\common;

use c00\common\Helper as H;

abstract class AbstractSettings
{
    const DEFAULT_KEY = 'settings';

    protected $key;
    protected $path;

    public function __construct($key = self::DEFAULT_KEY, $path = __DIR__){

        if (substr($path, strlen($path) - 1) !== DIRECTORY_SEPARATOR) $path .= DIRECTORY_SEPARATOR;

        $this->key = $key;
        $this->path = $path;
    }

    protected function getFilePath(){
        return $this->path . $this->key . '.json';
    }

    public function save(){

        $destination = $this->getFilePath();

        if (!file_exists($this->path)) {
            if (!mkdir($this->path, 0770, true)){
                throw new \Exception("Can't create folder to save settings");
            }
        }

        //convert to nice array
        $arrayVersion = $this->toArray();

        if (file_put_contents($destination, json_encode($arrayVersion, JSON_PRETTY_PRINT)) === false){
            throw new \Exception("Can't write settings file");
        }

        return true;
    }

    /**
     * Will convert an object into an array.
     *
     * In the process it will clean out anything that's not defined in the class definition.
     * @return array
     */
    private function toArray(){
        $internalFields = get_class_vars(self::class);

        $result = [];
        foreach(get_class_vars(static::class) as $key => $value){
            if((!isset($this->$key) || $this->$key === null)) {
                //Go to the next one.
                continue;
            }

            //Filter out internal stuff.
            if (isset($internalFields[$key])) continue;

            $result[$key] = $this->valueToArray($this->$key);
        }
        return $result;
    }

    private function valueToArray($value){

        //toArray on nested values
        if (is_object($value) && $value instanceof IDatabaseObject){
            //To Array on nested DatabaseObjects
            $return = $value->toArray();
            $return['__class'] = get_class($value);
        } else if (is_object($value) && $value instanceof IDatabaseProperty){
            //To Db on DatabaseProperties
            $return = $value->toDb();
            $return['__class'] = get_class($value);
        } else if (is_object($value)) {
            $return = H::objectToArray($value);
            $return['__class'] = get_class($value);
        } else if (is_array($value)){
            //Traverse array
            $return = array_map(function($v){ return $this->valueToArray($v); }, $value);
        } else if (is_bool($value)){
            //Convert bools to 1 or 0
            $return = ($value);
        } else {
            $return = $value;
        }

        return $return;
    }

    public function load(){
        $this->loadDefaults();

        $file = $this->getFilePath();
        if (!file_exists($file)) return false;

        $json =  file_get_contents($file);

        if (!is_string($json)) return false;

        $array = json_decode($json, true);

        if ($array === null || !is_array($array)) return false;

        //copy properties to Settings object
        $class_vars = get_class_vars(get_class($this));

        foreach ($class_vars as $name => $value) {
            if (isset($array[$name])){
                $this->$name = $this->loadValue($array[$name]);
            }
        }

        return true;
    }

    private function loadValue($array){
        //If it's just a value or a normal array, return that.
        if ($array === null || !is_array($array)) return $array;

        //Check if it's an array
        if (!isset($array['__class'])) {
            return array_map(function($v){ return $this->loadValue($v); }, $array);
        }

        /* We got here, so right now we have an array that should be a class.
         *
         * IDatabaseObject should be instantiated with fromArray()
         * IDatabaseProperty should be instantiated with fromDb()
         * Everything else, just instantiate object, and transfer properties.
         */

        $className = $array['__class'];
        $object = new $className();

        if ($object instanceof IDatabaseObject) {
            return $className::fromArray($array);
        } else if ($object instanceof IDatabaseProperty) {
            return $className::fromDb($array);
        }

        //Else, just instantiate
        $classVars = get_class_vars($className);
        foreach ($classVars as $name => $value) {
            if (isset($array[$name])){
                $object->$name = $array[$name];
            }
        }

        return $object;
    }

    public abstract function loadDefaults();
}