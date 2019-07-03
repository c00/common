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
class CovleDate implements IDatabaseProperty
{

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

    public function toMongoDate(){
        $result = new \MongoDate(strtotime($this->dateTime->format('Y-m-d H:i:s')));
        return $result;
    }

    public static function fromMongoDate(\MongoDate $md){
        if ($md === null) return new CovleDate();
        return CovleDate::fromString(date('Y-m-d H:i:s', $md->sec));
    }

    public static function newInstance(DateTime $dt = null){
        return new CovleDate($dt);
    }

    public static function yesterday(){
        return CovleDate::newInstance()->addDays(-1);
    }

    public static function tomorrow(){
        return CovleDate::newInstance()->addDays(1);
    }

    public static function now(){
        return CovleDate::newInstance();
    }

    public static function fromSeconds($seconds){
        if (!is_numeric($seconds)) $seconds = 0;
        
        $dt = new DateTime();
        $dt->setTimestamp($seconds);

        return new CovleDate($dt);
    }

    public function toSeconds(){
        return $this->dateTime->getTimestamp();
    }

    public function toMiliseconds(){
        return $this->dateTime->getTimestamp() * 1000;
    }

    public function cloneDate(){
        return CovleDate::fromSeconds($this->toSeconds());
    }

    public function getStartOfDay(){
        $date = $this->cloneDate();
        $date->dateTime->setTime(0,0,0);
        return $date;
    }

    public function getStartOfMonth(){
        $date = $this->cloneDate()
            ->getStartOfDay();

        $date->dateTime->setDate($this->getYear(), $this->getMonth(), 1);
        return $date;
    }

    public function getEndOfMonth(){
        $date = $this->cloneDate()
            ->getStartOfMonth()
            ->addMonths(1)
            ->addSeconds(-1);

        return $date;
    }

    public function getYear(){
        return $this->dateTime->format("Y");
    }

    public function getMonth(){
        return $this->dateTime->format("m");
    }

    public function getDay(){
        return $this->dateTime->format("d");
    }

    public function getHour(){
        return $this->dateTime->format("H");
    }

    public function getMinutes(){
        return $this->dateTime->format("i");
    }

    public function getSeconds(){
        return $this->dateTime->format("s");
    }

    public function isLaterThan(CovleDate $date){
        return ($this->toSeconds() > $date->toSeconds());
    }

    public function isSameOrLaterThan(CovleDate $date){
        return ($this->toSeconds() >= $date->toSeconds());
    }

    public function isEarlierThan(CovleDate $date){
        return ($this->toSeconds() < $date->toSeconds());
    }

    public function isSameOrEarlierThan(CovleDate $date){
        return ($this->toSeconds() <= $date->toSeconds());
    }

    public function equals(CovleDate $date){
        return ($date->toSeconds() == $this->toSeconds());
    }

    public function isPast(){
        $today = new CovleDate();
        $todayMs = $today->toSeconds();

        return ($todayMs > $this->toSeconds());
    }

    public function isFuture(){
        $today = new CovleDate();
        $todayMs = $today->toSeconds();

        return ($todayMs < $this->toSeconds());
    }

    public function toFriendlyString(){
        return $this->dateTime->format('F d, Y');
    }

    public function toString($format = 'Y-m-d H:i:s'){
        return $this->dateTime->format($format);
    }

    public function toDateString(){
        return $this->toW3cString();
    }

    public function dayOfMonth(){
        return $this->dateTime->format('d');
    }

    public function toW3cString(){
        return $this->dateTime->format('Y-m-d');
    }

    public static function fromMilliseconds($milliSeconds){
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
    public static function fromJavaScript($milliSeconds){
        return self::fromMilliseconds($milliSeconds);
    }

    public static function fromMailgun($timestamp){
        $split = explode('.', $timestamp);
        if (count($split) == 0) return new CovleDate();

        $seconds = $split[0];

        if (!is_numeric($seconds)) $seconds = 0;

        $dt = new DateTime(date('Y-m-d H:i:s', $seconds));
        return new CovleDate($dt);
    }

    public static function fromString($s, $format = "Y-m-d H:i:s"){
        $dt = DateTime::createFromFormat($format, $s);
        return new CovleDate($dt);
    }

    public static function fromDateString($s){
        $dt = DateTime::createFromFormat("Y-m-d", $s);
        $d = new CovleDate($dt);
        return $d->getStartOfDay();
    }

    public function setDayOfMonth($day){
        $this->dateTime->setDate($this->dateTime->format('Y'), $this->dateTime->format('m'), $day);
        return $this;
    }

    /** Add seconds to the date.
     *
     * Adds them to the object. Returns itself for chaining. Can be negative to subtract seconds.
     * @param $seconds int The number of days
     * @return CovleDate
     */
    public function addSeconds($seconds){
        $this->dateTime->add(DateInterval::createFromDateString("$seconds Seconds"));
        return $this;
    }

    /** Add minutes to the date.
     *
     * Adds them to the object. Returns itself for chaining. Can be negative to subtract minutes.
     * @param $minutes int The number of days
     * @return CovleDate
     */
    public function addMinutes($minutes){
        $this->dateTime->add(DateInterval::createFromDateString("$minutes Minutes"));
        return $this;
    }

    /** Add hours to the date.
     *
     * Adds them to the object. Returns itself for chaining. Can be negative to subtract hours.
     * @param $hours int The number of days
     * @return CovleDate
     */
    public function addHours($hours){
        $this->dateTime->add(DateInterval::createFromDateString("$hours Hours"));
        return $this;
    }

    /** Add days to the date.
     *
     * Adds them to the object. Returns itself for chaining. Can be negative to subtract days.
     * @param $days int The number of days
     * @return CovleDate
     */
    public function addDays($days){
        $this->dateTime->add(DateInterval::createFromDateString("$days Days"));
        return $this;
    }

    /** Add months to the date.
     *
     * Adds them to the object. Returns itself for chaining. Can be negative to subtract months.
     * @param $months int The number of days
     * @return CovleDate
     */
    public function addMonths($months){
        $this->dateTime->add(DateInterval::createFromDateString("$months Months"));
        return $this;
    }

    /** Add years to the date.
     *
     * Adds them to the object. Returns itself for chaining. Can be negative to subtract years.
     * @param $years int The number of days
     * @return CovleDate
     */
    public function addYears($years){
        $this->dateTime->add(DateInterval::createFromDateString("$years Years"));
        return $this;
    }

    /**
     * @param $date CovleDate
     * @return DateInterval
     */
    public function diff($date){
        return $this->dateTime->diff($date->dateTime);
    }

    /** Turns a database value (column) into an object.
     * @param int $value Time in seconds
     * @return IDatabaseProperty
     */
    public static function fromDb($value)
    {
        return self::fromSeconds($value);
    }

    /**
     * @return int Time in seconds
     */
    public function toDb()
    {
        return $this->toSeconds();
    }
}