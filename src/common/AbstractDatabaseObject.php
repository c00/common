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
        'double',
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

}