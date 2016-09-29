<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 17/06/2016
 * Time: 10:36
 */

namespace c00\QueryBuilder;
use \Exception;

class QueryBuilderException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {

        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}