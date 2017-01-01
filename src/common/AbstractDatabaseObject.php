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
    private $scalarTypes = [
        'int',
        'float',
        'string',
        'bool'
    ];
    /** @var  array */
    protected $_mapping = [];
    protected $_dataTypes = [];

    /** Turns an array into an instance of @static
     * @param $array array
     * @return static
     */
    public static function fromArray($array)
    {
        $o = new static;
        $o->objectFromArray($array);

        return $o;
    }

    /** Converts the object into an array.
     * Use this to 'prepare' an object to go into the database. Override this function to do any transformations.
     * @param $keepNulls bool Switch to keep Null values or omit them from the result.
     * @return array
     */
    public function toArray($keepNulls = false)
    {
        $mapping = (is_array($this->_mapping)) ? $this->_mapping : [];

        $internalFields = get_class_vars(self::class);

        $result = [];
        foreach(get_class_vars(static::class) as $key => $value){
            if((!isset($this->$key) || $this->$key === null) && !$keepNulls) {
                //Go to the next one.
                continue;
            }

            //Filter out internal stuff.
            if (isset($internalFields[$key])) continue;

            //Get mapped column name.
            $column = (isset($mapping[$key])) ? $mapping[$key] : $key;

            //toArray on nested values
            if (is_object($this->$key) && $this->$key instanceof IDatabaseObject){
                //To Array on nested DatabaseObjects
                $result[$column] = $this->$key->toArray();
            } else if (is_object($this->$key) && $this->$key instanceof IDatabaseProperty){
                //To Db on DatabaseProperties
                $result[$column] = $this->$key->toDb();
            } else if (is_bool($this->$key)){
                //Convert bools to 1 or 0
                $result[$column] = ($this->$key) ? 1 : 0;
            } else {
                $result[$column] = $this->$key;
            }


        }
        return $result;
    }

    /** Converts the object into an array that can be passed on to a client.
     * Use this to 'prepare' an object to go to a client. Override this function to do any transformations.
     * @return array
     */
    public function toShowable()
    {

        $array = H::objectToArray($this);

        return $array;
    }


    //region Mapping and DataTypes
    public function _getMapping(){
        return $this->_mapping;
    }

    public function _getDataTypes(){
        return $this->_dataTypes;
    }

    /** Maps a property of the object to a database column.
     * @param $objectProperty string The name of the property
     * @param $column string The name of the database column.
     */
    protected function mapProperty($objectProperty, $column){
        if (!is_string($objectProperty) || !is_string($column)){
            throw new \InvalidArgumentException("Object Properties and Columns can only be string values.");
        }

        if (!property_exists(static::class, $objectProperty)){
            throw new \InvalidArgumentException("Invalid property: $objectProperty");
        }

        $this->_mapping[$objectProperty] = $column;
    }

    /** Map multiple properties
     * @param array $mapping property => column.
     * @param bool $reset
     */
    protected function mapProperties(array $mapping, $reset = false){
        if ($reset){
            $this->_mapping = [];
        }
        foreach ($mapping as $property => $column) {
            $this->mapProperty($property, $column);
        }
    }

    protected function setPropertyType($objectProperty, $type){
        if (!$this->isScalarType($type) &&
            (!class_exists($type) || !$this->implementsDatabaseProperty($type))){
            throw new \Exception("Class not found or doesn't implement IDatabaseProperty: $type");
        }
        if (!property_exists(static::class, $objectProperty)){
            throw new \InvalidArgumentException("Invalid property: $objectProperty");
        }

        $this->_dataTypes[$objectProperty] = $type;
    }

    /**
     * @param array $types property => dataType
     * @param bool $reset
     */
    protected function setPropertyTypes(array $types, $reset = false){
        if ($reset){
            $this->_dataTypes = [];
        }

        foreach ($types as $property => $type) {
            $this->setPropertyType($property, $type);
        }
    }

    private function isScalarType($type){
        return in_array($type, $this->scalarTypes);
    }

    private function implementsDatabaseProperty($type){
        return in_array(IDatabaseProperty::class, class_implements($type));
    }
    //endregion

    //region Helpers
    /** Attempts to convert an array into an object.
     * @param $array array The array to convert
     * @return bool Result
     */
    private function objectFromArray($array){
        if (!is_array($array)) return false;

        $mapping = (is_array($this->_mapping)) ? $this->_mapping : [];



        $class_vars = get_class_vars(static::class);
        foreach ($class_vars as $name => $item) {
            //Is there a mapping entry?
            $column = (isset($mapping[$name])) ? $mapping[$name] : $name;

            if (isset($array[$column])) {
                //Get the value in the correct type.
                $value = $this->toType($name, $array[$column]);

                $this->$name = $value;
            }
        }

        return true;
    }

    private function toType($key, $value){
        //Do we have a mapping?
        if (!is_array($this->_dataTypes)) return $value;
        if (count($this->_dataTypes) == 0) return $value;

        //Get the desired type
        $type = H::getArrayValue($this->_dataTypes, $key, null);
        if (!$type) return $value;

        if (!$this->isScalarType($type)){
            //DatabaseProperty?
            if ($this->implementsDatabaseProperty($type)){
                return $type::fromDb($value);
            }
        } else if ($type == 'int') {
            return (int) $value;
        } else if ($type == 'float') {
            return (float) $value;
        } else if ($type == 'string') {
            return (string) $value;
        } else if ($type == 'bool') {
            return (bool) $value;
        }


        //Else Just return as string.
        return $value;
    }

    //endregion


}