<?php
/**
 * Created by PhpStorm.
 * User: danny
 * Date: 6/23/14
 * Time: 11:38 PM
 */

namespace Gedcomx\Util;


class DurationTest extends \PHPUnit_Framework_TestCase {

    public function goodProvider()
    {
        $data = array();
        $data[] = array("P0001Y", 1, null, null, null, null, null);
        $data[] = array("P12M", null, 12, null, null, null, null);
        $data[] = array("P01M", null, 1, null, null, null, null);
        $data[] = array("P31D", null, null, 31, null, null, null);
        $data[] = array("P01D", null, null, 1, null, null, null);
        $data[] = array("P0061Y12M31D", 61, 12, 31, null, null, null);
        $data[] = array("PT22H", null, null, null, 22, null, null);
        $data[] = array("PT59M", null, null, null, null, 59, null);
        $data[] = array("PT31S", null, null, null, null, null, 31);
        $data[] = array("PT22H59M31S", null, null, null, 22, 59, 31);
        $data[] = array("P0061Y12M31DT22H59M31S", 61, 12, 31, 22, 59, 31);
        return $data;
    }

    /**
     * @dataProvider goodProvider
     * @param $durationString
     * @param $year
     * @param $month
     * @param $day
     * @param $hour
     * @param $minute
     * @param $second
     */
    public function testGoodDuration($durationString, $year, $month, $day, $hour, $minute, $second)
    {
        $duration = new Duration();
        $duration->parse($durationString);
        $this->assertEquals($year, $duration->getYear(), "Year");
        $this->assertEquals($month, $duration->getMonth(), "Month");
        $this->assertEquals($day, $duration->getDay(), "Day");
        $this->assertEquals($hour, $duration->getHour(), "Hour");
        $this->assertEquals($minute, $duration->getMinute(), "Minute");
        $this->assertEquals($second, $duration->getSecond(), "Second");

        $roundtrip = $duration->__toString();
        $this->assertEquals($durationString, $roundtrip, "Roundtrip");
    }

    public function badProvider()
    {
        $data = array();
        $data['wrong prefix'] = array("X12M"); // wrong prefix
        $data['no prefix'] = array("12M"); // no prefix
        $data['failed to zero-pad year'] = array("P2Y"); // failed to zero-pad year
        $data['failed to include "T" before time elements'] = array("P31S"); // failed to include "T" before time elements.
        return $data;
    }
    /**
     * @dataProvider badProvider
     * @param $durationString
     */
    public function testBadDuration($durationString)
    {
        $duration = new Duration();
        $duration->parse($durationString);
        $this->assertFalse($duration->isValid());
    }
}
 
