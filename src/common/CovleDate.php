<?php
namespace c00\common;

use \DateTime, \DateInterval;
/**
 * Class CovleDate
 * Author: Co van Leeuwen
 * Email: Coo@covle.com
 *
 * This is a date helper class. Used to manipulate and compare dates more natural than standard PHP.
 *
 *
 * License: MIT
 * This comes as-is, no guarantees, no support, use at your own risk.
 */
class CovleDate{

    /**
     * @var DateTime
     */
    var $dateTime;

    public function __construct(DateTime $dt = null){
        //Create new now
        if (!$dt){
            $this->dateTime = new DateTime();
        }else{
            $this->dateTime = $dt;
        }
    }

    function toMongoDate(){
        $result = new \MongoDate(strtotime($this->dateTime->format('Y-m-d H:i:s')));
        return $result;
    }

    static function fromMongoDate(\MongoDate $md){
        if ($md === null) return new CovleDate();
        return CovleDate::fromString(date('Y-m-d H:i:s', $md->sec));
    }

    static function newInstance(DateTime $dt = null){
        return new CovleDate($dt);
    }

    static function yesterday(){
        return CovleDate::newInstance()->addDays(-1);
    }

    static function tomorrow(){
        return CovleDate::newInstance()->addDays(1);
    }

    static function now(){
        return CovleDate::newInstance();
    }

    static function fromSeconds($seconds){
        if (!is_numeric($seconds)) $seconds = 0;
        
        $dt = new DateTime();
        $dt->setTimestamp($seconds);

        return new CovleDate($dt);
    }

    function toSeconds(){
        return $this->dateTime->getTimestamp();
    }

    function toMiliseconds(){
        return $this->dateTime->getTimestamp() * 1000;
    }

    function cloneDate(){
        return CovleDate::fromSeconds($this->toSeconds());
    }

    function getStartOfDay(){
        return new CovleDate($this->dateTime->setTime(0,0,0));

    }

    function isLaterThan(CovleDate $date){
        return ($this->toSeconds() > $date->toSeconds());
    }

    function isEarlierThan(CovleDate $date){
        return ($this->toSeconds() < $date->toSeconds());
    }

    function equals(CovleDate $date){
        return ($date->toSeconds() == $this->toSeconds());
    }

    function isPast(){
        $today = new CovleDate();
        $todayMs = $today->toSeconds();

        return ($todayMs > $this->toSeconds());
    }

    function isFuture(){
        $today = new CovleDate();
        $todayMs = $today->toSeconds();

        return ($todayMs < $this->toSeconds());
    }

    function toFriendlyString(){
        return $this->dateTime->format('F d, Y');
    }

    function toString(){
        return $this->dateTime->format('Y-m-d H:i:s');
    }

    function toDateString(){
        return $this->toW3cString();
    }

    function dayOfMonth(){
        return $this->dateTime->format('d');
    }

    function toW3cString(){
        return $this->dateTime->format('Y-m-d');
    }

    static function fromMilliseconds($milliSeconds){
        if (!is_numeric($milliSeconds)) $milliSeconds = 0;

        $seconds = $milliSeconds / 1000;
        $dt = new DateTime(date('Y-m-d H:i:s', $seconds));
        return new CovleDate($dt);
    }

    /**
     * @param $milliSeconds
     * @return CovleDate
     * @deprecated use FromMilliseconds() instead
     */
    static function fromJavaScript($milliSeconds){
        return self::fromMilliseconds($milliSeconds);
    }

    static function fromMailgun($timestamp){
        $split = explode('.', $timestamp);
        if (count($split) == 0) return new CovleDate();

        $seconds = $split[0];

        if (!is_numeric($seconds)) $seconds = 0;

        $dt = new DateTime(date('Y-m-d H:i:s', $seconds));
        return new CovleDate($dt);
    }

    static function fromString($s){
        $dt = DateTime::createFromFormat("Y-m-d H:i:s", $s);
        return new CovleDate($dt);
    }

    static function fromDateString($s){
        $dt = DateTime::createFromFormat("Y-m-d", $s);
        $d = new CovleDate($dt);
        return $d->getStartOfDay();
    }

    function setDayOfMonth($day){
        $this->dateTime->setDate($this->dateTime->format('Y'), $this->dateTime->format('m'), $day);
        return $this;
    }

    function addSeconds($seconds){
        $this->dateTime->add(DateInterval::createFromDateString("$seconds Seconds"));
        return $this;
    }

    function addMinutes($minutes){
        $this->dateTime->add(DateInterval::createFromDateString("$minutes Minutes"));
        return $this;
    }

    function addHours($hours){
        $this->dateTime->add(DateInterval::createFromDateString("$hours Hours"));
        return $this;
    }

    function addDays($days){
        $this->dateTime->add(DateInterval::createFromDateString("$days Days"));
        return $this;
    }

    function addMonths($months){
        $this->dateTime->add(DateInterval::createFromDateString("$months Months"));
        return $this;
    }

    function addYears($years){
        $this->dateTime->add(DateInterval::createFromDateString("$years Years"));
        return $this;
    }
}