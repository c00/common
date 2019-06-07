<?php

namespace c00\QueryBuilder;


class DebugInfo
{
    const TYPE_TRANSACTION = 'transaction';

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
     * @param $q Qry|string
     */
    public function finish($q){
        $this->end = microtime(true);

        if (is_string($q)) {
            $this->sql = $q;
        } else if (get_class($q) === Qry::class) {
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
    }

    /**
     * @return float Seconds
     */
    public function getDifference()
    {
        if (!$this->start || !$this->end) return -1;

        return $this->end - $this->start;
    }
}