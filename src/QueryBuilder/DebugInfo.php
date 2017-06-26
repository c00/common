<?php
/**
 * Created by PhpStorm.
 * User: coo
 * Date: 26/06/17
 * Time: 12:54
 */

namespace c00\QueryBuilder;


use c00\common\CovleDate;

class DebugInfo
{
    public $sql;
    public $start;
    public $end;

    /**
     * @return DebugInfo
     */
    public static function start()
    {
        $di = new DebugInfo();
        $di->start = microtime(true);

        return $di;
    }

    /**
     * @param $q Qry
     */
    public function finish($q){
        $this->end = microtime(true);
        $this->sql = $q->sql;
        $params = $q->getParams();
        foreach ($params as $key => $value) {
            //Should have quotes?
            // NOTE: This is by no means a solution for all, but it's a 'good enough' for debug purposes.
            $addQuotes = (is_string($value));
            $value = ($addQuotes) ? "'$value'" : $value;

            $this->sql = str_replace(":$key", $value, $this->sql);
        }
    }

    public function getDifference(){
        if (!$this->start || !$this->end) return -1;

        return $this->end - $this->start;
    }
}