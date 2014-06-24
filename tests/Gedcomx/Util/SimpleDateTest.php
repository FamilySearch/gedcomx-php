<?php
/**
 * Created by PhpStorm.
 * User: danny
 * Date: 6/23/14
 * Time: 10:18 PM
 */

namespace Gedcomx\Util;


class SimpleDateTest extends \PHPUnit_Framework_TestCase {


    //


    public function goodProvider()
    {
        $data = array();
        $data[] = array("+1820", 1820, null, null, null, null, null, false, null, null);
        $data[] = array("-0090", -90, null, null, null, null, null, false, null, null);
        $data[] = array("+1820-12", 1820, 12, null, null, null, null, false, null, null);
        $data[] = array("+1820-12-31", 1820, 12, 31, null, null, null, false, null, null);
        $data[] = array("+1820-12-31T12", 1820, 12, 31, 12, null, null, false, null, null);
        $data[] = array("+1820-12-31T00", 1820, 12, 31, 0, null, null, false, null, null);
        $data[] = array("+1820-12-31T23", 1820, 12, 31, 23, null, null, false, null, null);
        $data[] = array("+1820-12-31T23:59", 1820, 12, 31, 23, 59, null, false, null, null);
        $data[] = array("+1820-12-31T23:59:01", 1820, 12, 31, 23, 59, 1, false, null, null);
        $data[] = array("+1820-12-31T23:59:01Z", 1820, 12, 31, 23, 59, 1, true, null, null);
        $data[] = array("+1820-12-31T23:59:01+12", 1820, 12, 31, 23, 59, 1, false, 12, null);
        $data[] = array("+1820-12-31T23:59:01+12:11", 1820, 12, 31, 23, 59, 1, false, 12, 11);
        $data[] = array("+1820-12-31T23:59:01+01", 1820, 12, 31, 23, 59, 1, false, 1, null);
        $data[] = array("+1820-12-31T23:59:01-01:30", 1820, 12, 31, 23, 59, 1, false, -1, 30);
        $data[] = array("+1820-12-31T23:59+12:11", 1820, 12, 31, 23, 59, null, false, 12, 11);
        $data[] = array("+1820-12-31T23+12:11", 1820, 12, 31, 23, null, null, false, 12, 11);
        return $data;
    }
    /**
     * @dataProvider goodProvider
     * @param $strDate
     * @param $year
     * @param $month
     * @param $day
     * @param $hour
     * @param $minute
     * @param $second
     * @param $isUTC
     * @param $tzHour
     * @param $tzMinute
     */
    public function testGoodSimpleDate($strDate, $year, $month, $day, $hour, $minute, $second, $isUTC, $tzHour, $tzMinute)
    {
        $simpleDate = new SimpleDate();
        $simpleDate->parse($strDate);
        $this->assertEquals($year, $simpleDate->getYear(), "Year");
        $this->assertEquals($month, $simpleDate->getMonth(), "Month");
        $this->assertEquals($day, $simpleDate->getDay(), "Day");
        $this->assertEquals($hour, $simpleDate->getHour(), "Hour");
        $this->assertEquals($minute, $simpleDate->getMinute(), "Minute");
        $this->assertEquals($second, $simpleDate->getSecond(), "Second");
        $this->assertEquals($isUTC, $simpleDate->getIsUTC(), "isUTC");
        $this->assertEquals($tzHour, $simpleDate->getTimeZoneHours(), "tzHour");
        $this->assertEquals($tzMinute, $simpleDate->getTimeZoneMinutes(), "tzMinute");
        $roundTrip = $simpleDate->__toString();
        $this->assertEquals($strDate, $roundTrip, "Round trip");
    }

    public function badProvider()
    {
        $data = array();
        $data['forgot the "+"'] = array("1820"); // forgot the "+"
//        $data['no time before the time zone.'] = array("+1820-12-31TZ"); // no time before the time zone. TODO - not working
        $data['no year'] = array("T12Z"); // no year
        $data['need to 0-pad the year to 4 digits'] = array("+978-12-31"); // need to 0-pad the year to 4 digits.
//        $data['need to 0-pad the month to 2 digits'] = array("+1978-3-03"); // need to 0-pad the month to 2 digits TODO - not working
//        $data['need to 0-pad the day to 2 digits'] = array("+1978-12-3"); // need to 0-pad the day to 2 digits TODO - not working
        return $data;
    }

    /**
     * @dataProvider badProvider
     * @param $strDate
     */
    public function testBadSimpleDate($strDate)
    {
        $simpleDate = new SimpleDate();
        $this->setExpectedException("Exception");
        $simpleDate->parse($strDate);
    }
}
 
