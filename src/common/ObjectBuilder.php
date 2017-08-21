<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 20/08/17
 * Time: 12:35
 */

namespace c00\common;


use c00\QueryBuilder\Qry;
use c00\QueryBuilder\QueryBuilderException;

class ObjectBuilder
{
    /** @var  Qry */
    public $q;
    public $types = [];

    public $objects = [];

    public function __construct()
    {
    }

    /**
     * @param $q Qry
     * @return ObjectBuilder
     */
    public static function newInstance($q) {
        $b = new ObjectBuilder();
        $b->q = $q;

        $b->types = $q->getClasses();

        return $b;
    }

    /** Build objects from array
     * @param $rows array One or more rows to be converted to objects.
     * @return ObjectBuilder
     */
    public function build($rows) {
        if (!is_array($rows)) $rows = [$rows];
        foreach ($rows as $row) {
            $this->buildRow($row);
        }

        return $this;
    }

    private function buildRow($row) {
        $all = [];
        $arrays = [];

        foreach ($row as $column => $value) {
            //build individual arrays

            $split = explode('.', $column);
            if (count($split) === 1) {
                //This is not an alias. Add for all
                $all[$column] = $value;
            } else if (count($split) === 2) {
                //property found.
                $table = $split[0];
                $column = $split[1];

                if (!isset($arrays[$table])) $arrays[$table] = [];
                $arrays[$table][$column] = $value;

            } else {
                throw new QueryBuilderException("More than 1 dot in column name. I can't handle this.");
            }
        }


        foreach ($arrays as $table => &$array) {
            //Add the 'all' arrays
            //Merge 'all' first, so that those values get overwritten by the specific table stuff.
            $array = array_merge($all, $array);

            // // Make them objects // //
            //Make sure we have a type for it.
            if (!isset($this->types[$table])) continue;
            $className = $this->types[$table];

            if (!in_array(IDatabaseObject::class, class_implements($className))){
                throw new QueryBuilderException("Class $className doesn't implement IDatabaseObject");
            }

            if (!isset($this->objects[$table])) $this->objects[$table] = [];
            /** @var IDatabaseObject $o */
            $o = $className::fromArray($array);
            $id = $o->getIdentifier();
            if ($id === null) throw new QueryBuilderException("Object of $className has no identifier");

            //Don't add doubles.
            if (isset($this->objects[$table][$id])) continue;
            $this->objects[$table][$id] = $o;
        }

    }

    public function nest() {
        //Nest objects inside each other.
        $main = null;
        foreach ($this->objects as $array) {
            $main = $array;
            break;
        }

        if (!$main) throw new QueryBuilderException("No objects... Did you call `build()` first?");

        // $main is an array of the main class.
        foreach ($main as $object) {
            $this->nestObject($object);
        }
    }

    /**
     * @param IDatabaseObject $object
     */
    private function nestObject($object) {
        //skip if it's not a AbstractDatabaseObject
        if (!$object instanceof AbstractDatabaseObject) return;

        $dataTypes = $this->getFilteredDataTypes($object);
        $joins = $this->q->getJoins();
        //Use joins to get the joined content
        $bla = ";;";

        //use datatypes to get the place to put them.

        //What i want is the class . property = otherclass . property


    }

    /** Filter dataTypes to those we can have.
     * @param $object AbstractDatabaseObject
     * @return array The filtered version of datatypes.
     */
    private function getFilteredDataTypes($object) {
        $dataTypes = $object->_getDataTypes();

        $filtered = [];

        $validTypes = array_flip($this->types);

        foreach ($dataTypes as $prop => $type) {
            if (isset($validTypes[$type])) $filtered[$prop] = $type;
        }

        return $filtered;
    }
}