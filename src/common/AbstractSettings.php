<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 30/03/2016
 * Time: 23:08
 */

namespace c00\common;

abstract class AbstractSettings
{
    protected $key;

    public function __construct($key){
        $this->key = $key;
    }

    private function getDefaultPath(){
     return $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
    }

    public function save($path = null){
        if ($path === null) $path = $this->getDefaultPath();

        $dest = $path . $this->key . '.json';

        if (!file_exists($path)) {
            if (!mkdir($path, 0770, true)){
                throw new \Exception("Can't create folder to save settings");
            }
        }

        if (file_put_contents($dest, json_encode($this, JSON_PRETTY_PRINT)) === false){
            throw new \Exception("Can't write settings file");
        }

        return true;
    }

    public function load($path = null){
        if ($path === null) $path = $this->getDefaultPath();
        $this->loadDefaults();


        $file = $path . $this->key . '.json';
        if (!file_exists($file)) return false;
        $json =  file_get_contents($file);



        if (!is_string($json)) return false;

        $array = json_decode($json, true);

        if ($array === null || !is_array($array)) return false;

        //copy properties to Settings object
        $class_vars = get_class_vars(get_class($this));

        foreach ($class_vars as $name => $value) {
            if (isset($array[$name])){
                $this->$name = $array[$name];
            }
        }

        return true;
    }

    public abstract function loadDefaults();
}