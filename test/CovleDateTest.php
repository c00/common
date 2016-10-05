<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;

use c00\common\CovleDate;

class CovleDateTest extends \PHPUnit_Framework_TestCase
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
        //Test now.
        $date = $this->testDate;

        $seconds = $date->toSeconds();
        $milliseconds = $date->toMiliseconds();
        $dateTimeString = $date->toString();
        $dateString = $date->toDateString();

        //Check types
        $this->assertTrue(is_int($seconds));
        $this->assertTrue(is_double($milliseconds));
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

    public function testOthers(){
        //Test adding of everything
    }


}