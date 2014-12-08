<?php
namespace Gedcomx\Util;

/**
 * Class representing a Duration in a GedcomX formal date.
 * The form of a Duration string is
 *
 *   P[yyyyY][mmM][ddD][T[hhH][mmM][ssS]]
 *
 * for a duration in years, months, days, hours, minutes and/or seconds.
 *
 * User: Danny Yeshurun (ported from Java code)
 *
 */
class Duration
{

    //  P[yyyyY][mmM][ddD][T[hhH][mmM][ssS]]
    private static $DURATION_PATTERN =
        "/P(\\d{4}Y)?(\\d{2}M)?(\\d{2}D)?(?:T(\\d{2}H)?(\\d{2}M)?(\\d{2}S)?)?/";

    /**
     * @var int
     */
    private $year;

    /**
     * @var int
     */
    private $month;

    /**
     * @var int
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
     * A parsing method that takes a valid duration string and parses it into a Duration object.
     * @param string $durationString of the form "P[yyyyY][mmM][ddD][T[hhH][mmM][ssS]]" that specifies a duration.
     * @throws \Exception
     */
    public function parse($durationString)
    {
        $matches = array();
        $status = preg_match(self::$DURATION_PATTERN, $durationString, $matches);
        if ($status === false) {
            throw new \Exception("Malformed simple date string {$durationString} must be P[yyyyY][mmM][ddD][T[hhH][mmM][ssS]]");
        }
        $this->year = $this->grabInt($matches, 1);
        $this->month = $this->grabInt($matches, 2);
        $this->day = $this->grabInt($matches, 3);
        $this->hour = $this->grabInt($matches, 4);
        $this->minute = $this->grabInt($matches, 5);
        $this->second = $this->grabInt($matches, 6);
    }

    /**
     * Convert the string in the given group to an Integer, unless there are not that many groups or the group is null.
     *
     * @param array $matches
     * @param int $group
     * @return int|null
     */
    private function grabInt($matches, $group)
    {
        if (count($matches) < $group + 1) {
            return null;
        } else {
            $s = $matches[$group];
            if (empty($s)) {
                return null;
            }
            // Strip off last character (e.g., "12H" -> "12")
            $s = substr($s, 0, strlen($s) - 1);
            return intval($s);
        }
    }

    /**
     * Tell whether the Duration is valid, which means that at least one of its values is non-null,
     * and none exceeds the 2-digit limit (or 4 digits for year).
     * @return true if valid, false otherwise.
     */
    public function isValid()
    {
        if ($this->year === null && $this->month === null && $this->day === null && $this->hour === null && $this->minute === null && $this->second === null) {
            return false; // must have at least one value to be valid.
        }
        // While we might expect the two-digit values to be more constrained (e.g., month=1..12; minute=1..59),
        // the format allows larger values, and perhaps a duration of 55 days is a reasonable thing to express.
        return self::ok($this->year, 0, 9999) && self::ok($this->month, 0, 99) && self::ok($this->day, 0, 99) && self::ok($this->hour, 0, 99) && self::ok($this->minute, 0, 99) && self::ok($this->second, 0, 99);
    }

    /**
     * Convert this Duration to the canonical string for a Duration, of the form:
     *   P[yyyyY][mmM][ddD][T[hhH][mmM][ssS]]
     * for use in a GedcomX formal date string.
     *
     * @return string
     */
    public function __toString()
    {
        $result = array();
        $result[] = "P";
        $result[] = $this->formatNumber($this->year, 4, "Y");
        $result[] = $this->formatNumber($this->month, 2, "M");
        $result[] = $this->formatNumber($this->day, 2, "D");
        if (($this->hour !== null) || ($this->minute !== null) || ($this->second !== null)) {
            $result[] = "T";
            $result[] = $this->formatNumber($this->hour, 2, "H");
            $result[] = $this->formatNumber($this->minute, 2, "M");
            $result[] = $this->formatNumber($this->second, 2, "S");
        }

        return implode("", $result);
    }

    /**
     * @param int|null $number
     * @param int $digits
     * @param string $suffix
     * @return string
     */
    private function formatNumber($number, $digits, $suffix)
    {
        if ($number === null) {
            return "";
        }
        $format = "%0{$digits}d{$suffix}";
        return sprintf($format, $number);
    }

    /**
     * Set the duration day.
     * @param int $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * Get the duration day, or null if not specified.
     * @return int
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set the duration hour.
     * @param int $hour
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
    }

    /**
     * Get the duration hour, or null if not specified.
     * @return int
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Set the duration minute.
     * @param int $minute
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;
    }

    /**
     * Get the duration minute, or null if not specified.
     * @return int
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Set the duration month.
     * @param int $month
     */
    public function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * Get the duration month, or null if not specified.
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set the duration second.
     * @param int $second
     */
    public function setSecond($second)
    {
        $this->second = $second;
    }

    /**
     * Get the duration second, or null if not specified.
     * @return int
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * Set the duration year.
     * @param int $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * Get the duration year, or null if not specified.
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Checks if a number is in range
     *
     * @param int|null $number
     * @param int $min
     * @param int $max
     * @return bool
     */
    private static function ok($number, $min, $max)
    {
        return ($number === null) || (($number >= $min) && ($number <= $max));
    }

}
