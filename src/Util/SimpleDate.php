<?php
namespace Gedcomx\Util;

/**
 * "Simple date" string is generally:
 *   (+|-)YYYY-MM-DDThh:mm:ss((+\-)hh:mm|Z)
 * or, using square brackets around optional parts:
 *   (+|-)YYYY[-MM[-DD[Thh[:mm[:ss[(+\-)hh[:mm]|Z]]]]]]
 *
 * User: danny yeshurun (ported from Java)
 * Date: 6/23/14
 * Time: 11:48 PM
 */
class SimpleDate
{

    /**
     * @var int  -9999 = 10000 B.C.; -1 = 2 B.C.; 0 = 1 B.C.; 1 = 1 A.D.; 9999 = 9999 A.D.
     */
    private $year;

    /**
     * @var int 1..12 = January..December; null => none
     */
    private $month;

    /**
     * @var int 1..31; null => none
     */
    private $day;

    /**
     * @var int
     */
    private $hour;

    /**
     * @var int
     */
    private $minute;

    /**
     * @var int
     */
    private $second;

    /**
     * @var bool
     */
    private $isUTC;

    /**
     * @var int null => no time zone
     */
    private $timeZoneHours;

    /**
     * @var int must be null if timeZoneHours is null.  null => 0.
     */
    private $timeZoneMinutes;

//    //(+|-)YYYY[-MM[-DD[Thh[:mm[:ss]][(+\-)hh[:mm]|Z]]]]
    private static $SIMPLE_DATE_PATTERN =
        "/((?:\\+|-)\\d{4})(?:-(\\d{2})(?:-(\\d{2})(?:T(\\d{2})(?::(\\d{2})(?::(\\d{2})?)?)?(Z|((?:\\+|-)\\d{2})(?::(\\d{2}))?)?)?)?)?/";


    /**
     * Tell whether this SimpleDate is valid.  In particular, make sure that there are no
     *   specific date or time parts for which the more general parts are null,
     *   and make sure that if isUTC is set, then the time zone hours and minutes are null.
     * @return true if the date looks valid, false if there is a problem.
     */
    public function isValid()
    {
        if ($this->timeZoneMinutes != null) {
            if ($this->timeZoneMinutes < 0 || $this->timeZoneMinutes > 59 || $this->timeZoneHours == null || $this->isUTC) {
                return false;
            }
        }
        if ($this->timeZoneHours != null) {
            if ($this->timeZoneHours < -23 || $this->timeZoneHours > 23 || $this->isUTC) {
                return false;
            }
        }
        if (self::hasProblem($this->second, 0, 59, $this->minute) || self::hasProblem($this->minute, 0, 59, $this->hour) || self::hasProblem($this->hour, 0, 23, $this->day) ||
            self::hasProblem($this->day, 1, 31, $this->month) || self::hasProblem($this->month, 1, 12, $this->year)
        ) {
            return false;
        }
        return true; // looks ok
    }

    /**
     * Tell whether the number has a problem, meaning that either it is non-null and
     *   is outside the bounds given, or else its more general "parent" is non-null.
     * @param number - number to check
     * @param min - minimum allowable value for the number.
     * @param max - maximum allowable value for the number.
     * @param moreGeneral - more general number (e.g., a month if the number is for a day)
     * @return true if everything looks
     */
    private static function hasProblem($number, $min, $max, $moreGeneral)
    {
        return $number != null && ($number < $min || $number > $max || $moreGeneral == null);
    }

    /**
     * Convert the string in the given group to an Integer, unless there are not that many groups or the group is null.
     * If the string begins with "+" then this character is stripped off.
     * @param $matches
     * @param $group
     * @return int|null
     */
    private function grabInt($matches, $group)
    {
        if (count($matches) < $group + 1) {
            return null;
        } else {
            $s = $matches[$group];
            if (empty($s)) { // === null) {
                return null;
            }
            if (substr($s, 0, 1) === "+") {
                $s = substr($s, 1);
            }
            return intval($s);
        }
    }

    /**
     * @param $simpleDateString
     * @throws \Exception
     */
    public function parse($simpleDateString)
    {
        $matches = array();
        $status = preg_match(self::$SIMPLE_DATE_PATTERN, $simpleDateString, $matches);
        if ($status === false || $status === 0) {
            throw new \Exception("Malformed simple date string {$simpleDateString} must be (+|-)YYYY[-MM[-DD[Thh[:mm[:ss]][Z|(+|-)hh[:mm]]]]]");
        }
        $this->year = $this->grabInt($matches, 1);
        $this->month = $this->grabInt($matches, 2);
        $this->day = $this->grabInt($matches, 3);
        $this->hour = $this->grabInt($matches, 4);
        $this->minute = $this->grabInt($matches, 5);
        $this->second = $this->grabInt($matches, 6);
        if ((count($matches) >= 8) && ($matches[7] === "Z")) {
            $this->isUTC = true;
        } else {
            $this->isUTC = false;
            $this->timeZoneHours = $this->grabInt($matches, 8);
            $this->timeZoneMinutes = $this->grabInt($matches, 9);
        }
    }

    /**
     * Convert the SimpleDate to a formal GedcomX simple date string, of the form:
     *   (+|-)YYYY[-MM[-DD[Thh[:mm[:ss]][(+\-)hh[:mm]|Z]]]]
     * @return string
     */
    public function __toString()
    {
        $result = array();
        $result[] = ($this->year >= 0) ? "+" : "-";
        $result[] = sprintf("%04d", abs($this->year));
        if ($this->month !== null) {
            $result[] = "-";
            $result[] = sprintf("%02d", $this->month);
            if ($this->day !== null) {
                $result[] = "-";
                $result[] = sprintf("%02d", $this->day);
                if ($this->hour !== null) {
                    $result[] = "T";
                    $result[] = sprintf("%02d", $this->hour);
                    if ($this->minute !== null) {
                        $result[] = ":";
                        $result[] = sprintf("%02d", $this->minute);
                        if ($this->second !== null) {
                            $result[] = ":";
                            $result[] = sprintf("%02d", $this->second);
                        }
                    }
                    if ($this->isUTC) {
                        $result[] = "Z";
                    } elseif ($this->timeZoneHours !== null) {
                        $result[] = ($this->timeZoneHours >= 0) ? "+" : "-";
                        $result[] = sprintf("%02d", abs($this->timeZoneHours));
                        if ($this->timeZoneMinutes !== null) {
                            $result[] = ":";
                            $result[] = $this->timeZoneMinutes;
                        }
                    }
                }
            }
        }
        return implode("", $result);
    }

    /**
     * Get the day of the month as an integer from 1..31.
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param int $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * Get the hour of the day as an integer from 0 (=midnight) to 23(=11 p.m.)
     * @return int
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * @param int $hour
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
    }

    /**
     * Get a flag for whether this SimpleDate uses a time in Universal Time Code (UTC),
     *   in which case "Z" is used in the string, and the time zone hours and minutes are ignored.
     * @return boolean
     */
    public function getIsUTC()
    {
        return $this->isUTC;
    }

    /**
     * @param boolean $isUTC
     */
    public function setIsUTC($isUTC)
    {
        $this->isUTC = $isUTC;
    }

    /**
     * Get the minute of the hour as an Integer (0..59).
     * Must be null if there is no hour.
     * @return int
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * @param int $minute
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;
    }

    /**
     * Get the month as an Integer, where 1=January and 12=December.
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @param int $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * Get the second of the minute as an Integer (0..59).
     * Must be null if there is no minute.
     * @return int
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * @param int $second
     */
    public function setSecond($second)
    {
        $this->second = $second;
    }

    /**
     * Get the hour offset from GMT of the time zone.  Ignored if isUTC or if the hours is null.
     * @return int
     */
    public function getTimeZoneHours()
    {
        return $this->timeZoneHours;
    }

    /**
     * @param int $timeZoneHours
     */
    public function setTimeZoneHours($timeZoneHours)
    {
        $this->timeZoneHours = $timeZoneHours;
    }

    /**
     * Get the hour minutes of the time zone.  Ignored if isUTC or if the timeZoneHours is null.
     * @return int
     */
    public function getTimeZoneMinutes()
    {
        return $this->timeZoneMinutes;
    }

    /**
     * @param int $timeZoneMinutes
     */
    public function setTimeZoneMinutes($timeZoneMinutes)
    {
        $this->timeZoneMinutes = $timeZoneMinutes;
    }

    /**
     * Get the year as an integer.  Positive years are treated as C.E. (A.D.);
     * Negative years are one less than the B.C.E. (B.C.) year.
     * For example, -9999 = 10000 B.C.; -1 = 2 B.C.; 0 = 1 B.C.; 1 = 1 A.D.; 2000 = 2000 A.D.; 9999 = 9999 A.D.
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }


}
