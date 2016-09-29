<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 19/06/2016
 * Time: 18:24
 */

namespace c00\common;
use c00\common\Helper as H;

class Message implements IDatabaseObject
{
    public $id, $teamId, $fromTeam, $text;
    /** @var CovleDate */
    public $date;

    /** Returns a Message
     * @param $array array
     * @return Message|bool
     */
    public static function fromArray($array)
    {
        /** @var Message $m */
        $m = H::objectFromArray($array, self::class);
        if (!$m) return false;

        //Fix date
        if (isset($array['date'])){
            $m->date = CovleDate::fromSeconds($array['date']);
        }

        //fix fromTeam
        $m->fromTeam = ($array['fromTeam'] == 1) ? true : false;

        return $m;
    }

    /** Converts the object into an array
     * @return array
     */
    public function toArray()
    {
        $array = H::objectToArray($this);
        $array['date'] = $this->date->toSeconds();
        $array['fromTeam'] = ($this->fromTeam) ? 1 : 0;

        return $array;
    }

    public function toShowable()
    {
        $a = H::objectToArray($this);
        $a['date'] = $this->date->toMiliSeconds();
        $a['id'] = (int) $this->id;

        return $a;
    }
}