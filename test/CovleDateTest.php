<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\common\CovleDate;
use PHPUnit\Framework\TestCase;

class CovleDateTest extends TestCase
{
    const TEST_DATE_SECONDS = 507303000;
    const TEST_DATETIME_STRING_GMT = "1986-01-28 13:30:00";
    const TEST_DATE_STRING_GMT = "1986-01-28";

    /** @var CovleDate */
    private $testDate;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);


        $this->testDate = CovleDate::fromSeconds(self::TEST_DATE_SECONDS);
    }

    public function testBasics(){
        //Test
        $date = $this->testDate;

        $seconds = $date->toSeconds();
        $milliseconds = $date->toMiliseconds();
        $dateTimeString = $date->toString();
        $dateString = $date->toDateString();

        //Check types
        $this->assertTrue(is_int($seconds));
        $this->assertTrue(is_int($milliseconds));
        $this->assertTrue(is_string($dateTimeString));
        $this->assertTrue(is_string($dateString));

        //Check values
        $this->assertEquals(self::TEST_DATE_SECONDS, $seconds);
        $this->assertEquals(self::TEST_DATE_SECONDS * 1000, $milliseconds);
        $this->assertEquals(self::TEST_DATETIME_STRING_GMT, $dateTimeString);
        $this->assertEquals(self::TEST_DATE_STRING_GMT, $dateString);

        //Instantiate
        $newDate1 = CovleDate::fromSeconds($seconds);
        $newDate2 = CovleDate::fromMilliseconds($milliseconds);
        $newDate3 = CovleDate::fromString($dateTimeString);
        $newDate4 = CovleDate::fromDateString($dateString);

        //Check values
        $this->assertTrue($newDate1->equals($date));
        $this->assertTrue($newDate2->equals($date));
        $this->assertTrue($newDate3->equals($date));
        $this->assertTrue($newDate4->equals($date->getStartOfDay()));
    }

    public function testFuture(){
        $now = CovleDate::now();
        $this->assertFalse($now->isFuture());

        $now->addMinutes(1);
        $this->assertTrue($now->isFuture());
    }

    public function testPast(){
        $aMomentAgo = CovleDate::now()->addMinutes(-1);
        $this->assertTrue($aMomentAgo->isPast());

        $aMomentAgo->addMinutes(1);
        $this->assertFalse($aMomentAgo->isPast());
    }

    public function testFluency(){
        $date = $this->testDate;
        $date2 = $date->addMinutes(10);

        //These are now both references to the same object.
        $this->assertSame($date, $date2);

        //These are copies.
        $date3 = $date->cloneDate();
        $this->assertNotSame($date, $date3);
        $this->assertTrue($date->equals($date3));
    }

    public function testParts(){
        $this->assertEquals("1986", $this->testDate->getYear());
        $this->assertEquals("01", $this->testDate->getMonth());
        $this->assertEquals("28", $this->testDate->getDay());

        $this->assertEquals("13", $this->testDate->getHour());
        $this->assertEquals("30", $this->testDate->getMinutes());
        $this->assertEquals("00", $this->testDate->getSeconds());
    }

    public function testStartOfMonth(){
        $beginning = $this->testDate->getStartOfMonth();

        $this->assertEquals("1986", $beginning->getYear());
        $this->assertEquals("01", $beginning->getMonth());
        $this->assertEquals("01", $beginning->getDay());

        $this->assertEquals("00", $beginning->getHour());
        $this->assertEquals("00", $beginning->getMinutes());
        $this->assertEquals("00", $beginning->getSeconds());

    }

    public function testEndOfMonth(){
        $end = $this->testDate->getEndOfMonth();

        $this->assertEquals("1986", $end->getYear());
        $this->assertEquals("01", $end->getMonth());
        $this->assertEquals("31", $end->getDay());

        $this->assertEquals("23", $end->getHour());
        $this->assertEquals("59", $end->getMinutes());
        $this->assertEquals("59", $end->getSeconds());

    }

    public function testEndOfMonth2(){
        //Test if leap years are done correctly.
        //Note: This all uses PHPs internal date calculation stuff, so this should work anyway. I'm reinventing the wheel, I promise.
        $leapMonth = CovleDate::fromString("2016-02-02 12:45:12");
        $end = $leapMonth->getEndOfMonth();

        $this->assertEquals("2016", $end->getYear());
        $this->assertEquals("02", $end->getMonth());
        $this->assertEquals("29", $end->getDay());

        $this->assertEquals("23", $end->getHour());
        $this->assertEquals("59", $end->getMinutes());
        $this->assertEquals("59", $end->getSeconds());

    }

    public function testStartOfDay(){
        $beginning = $this->testDate->getStartOfDay();

        $this->assertEquals("1986", $beginning->getYear());
        $this->assertEquals("01", $beginning->getMonth());
        $this->assertEquals("28", $beginning->getDay());

        $this->assertEquals("00", $beginning->getHour());
        $this->assertEquals("00", $beginning->getMinutes());
        $this->assertEquals("00", $beginning->getSeconds());
    }

    public function testStartOfDayBug(){
        $date = CovleDate::fromString(self::TEST_DATETIME_STRING_GMT);

        $this->assertEquals(self::TEST_DATETIME_STRING_GMT, $date->toString());
        $date2 = $date->getStartOfDay();

        //This will fail, because we alter the first object...
        $this->assertEquals(self::TEST_DATETIME_STRING_GMT, $date->toString());
    }

    public function testDiff(){
        $date1 = CovleDate::fromString(self::TEST_DATETIME_STRING_GMT);
        $date2 = $date1
            ->cloneDate()
            ->addDays(2)
            ->addMonths(1)
            ->addYears(1);

        $diff = $date1->diff($date2);

        $expectedDays = 2 + 31 + 365; //2 days, 1 month (january, 31 days), 1 year (365 days, no leap year)
        $this->assertEquals($expectedDays, $diff->days);

    }


}