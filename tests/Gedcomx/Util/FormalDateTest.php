<?php
/**
 * Created by PhpStorm.
 * User: danny
 * Date: 6/24/14
 * Time: 9:12 AM
 */

namespace Gedcomx\Util;


class FormalDateTest extends \PHPUnit_Framework_TestCase {


    public function goodProvider()
    {
        $data = array();
        $data[] = array("+1820", false, 1820, false, null, null, null);
        $data[] = array("A+1820", true, 1820, false, null, null, null);
//        $data[] = array("1820"); // no "+"
//        $data[] = array("+1820--12"); // no month

        // Ranges
        $data[] = array("A+1820/", true, 1820, true, null, null, null);
        $data[] = array("A+1820/+1830", true, 1820, true, 1830, null, null);
        $data[] = array("A/+1830", true, null, true, 1830, null, null);
        $data[] = array("/+1830", false, null, true, 1830, null, null);

        // Duration
        $data[] = array("+1820/P0061Y", false, 1820, true, null, 61, null);
        $data[] = array("A+1820/P0061Y", true, 1820, true, null, 61, null);
//        $data[] = array("/P0061Y"); // no start date
//        $data[] = array("A+1820/P61Y"); // need to 0-pad duration

        // Repetition
        $data[] = array("R/+1820/+1821", false, 1820, true, 1821, null, 0);
        $data[] = array("R42/+1820/+1821", false, 1820, true, 1821, null, 42);
        $data[] = array("R42/+1820/P0001Y", false, 1820, true, null, 1, 42);
        $data[] = array("R/+1820/P0061Y", false, 1820, true, null, 61, 0);
//        $data[] = array("AR/+1820/P0061Y"); // can't have approximate recurring date
//        $data[] = array("RA/+1820/P0061Y"); // can't have approximate recurring date
        return $data;
    }
    /**
     * @dataProvider goodProvider
     * @param $formalDateString
     * @param $isApproximate
     * @param $startYear
     * @param $isRange
     * @param $endYear
     * @param $durationYears
     * @param $repetitions
     */
    public function testGoodFormatDate($formalDateString, $isApproximate, $startYear, $isRange, $endYear, $durationYears, $repetitions)
    {
        $formalDate = new FormalDate();
        $formalDate->parse($formalDateString);
        $this->assertEquals($isApproximate, $formalDate->getIsApproximate(), "isApproximate");
        if ($startYear !== null) {
            $this->assertEquals($startYear, $formalDate->getStart()->getYear(), "startYear");
        }
        $this->assertEquals($isRange, $formalDate->getIsRange(), "isRange");
        if ($endYear !== null) {
            $this->assertEquals($endYear, $formalDate->getEnd()->getYear(), "endYear");
        }
        if ($durationYears !== null) {
            $this->assertEquals($durationYears, $formalDate->getDuration()->getYear(), "durationYear");
        }
        $this->assertEquals($repetitions, $formalDate->getNumRepetitions(), "repetitions");
        $roundtrip = $formalDate->__toString();
        $this->assertEquals($formalDateString, $roundtrip, "roundtrip");
    }

    public function badProvider()
    {
        $data = array();
        $data["No +"] = array("1820", true); // no "+"
//        $data["No month"] = array("+1820--12", false); // no month TODO - not working

        // Duration
        $data["No start date"] = array("/P0061Y", true); // no start date
        $data["Need to 0-pad duration"] = array("A+1820/P61Y", false); // need to 0-pad duration

        // Repetition
        $data["can't have approximate recurring date 1"] = array("AR/+1820/P0061Y", true); // can't have approximate recurring date
        $data["can't have approximate recurring date 2"] = array("RA/+1820/P0061Y", true); // can't have approximate recurring date
        return $data;
    }

    /**
     * @dataProvider badProvider
     * @param $formalDateString
     */
    public function testBadFormalDate($formalDateString, $expectException)
    {
        $formalDate = new FormalDate();
        if ($expectException) {
            $this->setExpectedException("Exception");
        }
        $formalDate->parse($formalDateString);
        $this->assertFalse($formalDate->isValid());
    }


}
 
