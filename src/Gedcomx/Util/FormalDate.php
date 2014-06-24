<?php
namespace Gedcomx\Util;

/**
 * Class representing a fully parsed GedcomX standard date, for the purpose of creating or understanding
 *   GedcomX formal date strings.
 * Format of a GedcomX formal dates are made up by strings of these types:
 *
 * Simple date:
 *   (+|-)YYYY[-MM[-DD[Thh[:mm[:ss[(+\-)hh[:mm]|Z]]]]]]
 *
 * Duration:
 *   P[yyyyY][mmM][ddD][T[hhH][mmM][ssS]]
 *
 * Closed date Range:
 *   [simpleDate]/[simpleDate|Duration]
 * Open-ended date range:
 *   [simpleDate]/
 *   /[simpleDate]
 * Recurring date
 *   R[repetitions]/simpleDate/(simpleDate|Duration)
 * Approximate date or date range
 *   A(simpleDate)
 *   A(dateRange)
 *
 * => ([A](simpleDate|dateRange) | R[repetitions]/simpleDate/(simpleDate|Duration))
 * => [A]simpleDate
 *    [A]simpleDate/[simpleDate|Duration]
 *    [A]/simpleDate
 *    R[repetitions]/simpleDate/(simpleDate|Duration)
 *
 * User: danny yeshurun (ported from Java)
 * Date: 6/23/14
 * Time: 11:48 PM
 *
 */
class FormalDate
{

    /**
     * Flag for whether this date or range is approximate
     * @var bool
     */
    private $isApproximate;

    /**
     * Flag for whether this FormalDate is a range.  If true, then a null 'end' and 'duration' indicates an open-ended range.
     * @var bool
     */
    private $isRange;

    /**
     * Starting time of a range, or the whole time when not a range.
     * @var SimpleDate
     */
    private $start;

    /**
     * End of a range (unless duration is used).  Must be null if !isRange or duration is non-null.
     * If isRange and duration is null and 'end' is null, then this is an open-ended range.
     * @var SimpleDate
     */
    private $end;

    /**
     * Duration of range.  Must be null if !isRange or 'end' is non-null.
     * @var Duration
     */
    private $duration;

    /**
     * Flag for whether this is a repeating date or not
     * @var bool
     */
    private $isRecurring;

    /**
     * number of repetitions.  null => no limit.
     * @var int
     */
    private $numRepetitions;

    private static $FORMAL_DATE_PATTERN = "/(A|R([0-9]*)\/)?([^\/]*)(\/([^\/]*))?/";

    /**
     * @param $formalDateString
     * @throws \Exception
     */
    public function parse($formalDateString)
    {
        $matches = array();
        $status = preg_match(self::$FORMAL_DATE_PATTERN, $formalDateString, $matches);
        if ($status === false) {
            throw new \Exception("Malformed simple date string {$formalDateString}");
        }
        // group 1: A or R[numRepetitions]
        if (isset($matches[1])) {
            if ($matches[1] === "A") {
                $this->isApproximate = true;
            } elseif (substr($matches[1], 0, 1) === "R") {
                $this->isRecurring = true;
                // Group 2: numRepetitions
                if (isset($matches[2]) && !empty($matches[2])) {
                    $this->numRepetitions = intval($matches[2]);
                }
            }
        }
        // Group 3: starting simpleDate
        if (isset($matches[3]) && !empty($matches[3])) {
            $this->start = new SimpleDate();
            $this->start->parse($matches[3]);
        }
        // Group 4: "/" and ending simpleDate or duration
        if (isset($matches[4])) {
            $this->isRange = true;
            if (isset($matches[5]) && !empty($matches[5])) {
                if (substr($matches[5], 0, 1) === "P") {
                    if ($this->start === null) {
                        throw new \Exception("Error: Cannot have duration without a starting date");
                    }
                    $this->duration = new Duration();
                    $this->duration->parse($matches[5]);
                } else {
                    $this->end = new SimpleDate();
                    $this->end->parse($matches[5]);
                }
            }
        }

    }

    /**
     * Tell whether the current state of the date is valid for a GedcomX formal date.
     * In particular, make sure it follows one of the following patterns:
     *    [A]simpleDate
     *    [A]simpleDate/[simpleDate|Duration]
     *    [A]/simpleDate
     *    R[repetitions]/simpleDate/(simpleDate|Duration)
     * @return bool
     */
    public function isValid()
    {
        if ($this->isRecurring) {
            return !$this->isApproximate && $this->start !== null && $this->start->isValid() && (($this->end !== null && $this->end->isValid()) || ($this->end === null && $this->duration !== null && $this->duration->isValid()));
        } else {
            if ($this->numRepetitions !== null) {
                return false;
            }
            if ($this->start !== null && !$this->isRange) {
                return $this->end === null && $this->duration === null && $this->start->isValid();
            }
            if ($this->isRange) {
                if ($this->start === null) {
                    return $this->end !== null && $this->end->isValid() && $this->duration === null;
                } else {
                    return
                        ($this->end === null || $this->duration === null) &&
                        ($this->end === null || $this->end->isValid()) &&
                        ($this->duration === null || $this->duration->isValid());
                }
            }
        }
        return false;
    }

    /**
     * Convert a FormalDate to a string
     * @return string
     */
    public function __toString()
    {
        $result = array();
        if ($this->isRecurring) {
            $result[] = "R";
            if ($this->numRepetitions !== null) {
                $result[] = $this->numRepetitions;
            }
            $result[] = "/";
        } elseif ($this->isApproximate) {
            $result[] = "A";
        }
        if ($this->start !== null) {
            $result[] = $this->start->__toString();
        }
        if ($this->isRange) {
            $result[] = "/";
        }
        if ($this->end !== null) {
            $result[] = $this->end->__toString();
        } elseif ($this->duration !== null) {
            $result[] = $this->duration->__toString();
        }
        return implode("", $result);
    }

    /**
     * @param \Gedcomx\Util\Duration $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return \Gedcomx\Util\Duration
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param \Gedcomx\Util\SimpleDate $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return \Gedcomx\Util\SimpleDate
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $isApproximate
     */
    public function setIsApproximate($isApproximate)
    {
        $this->isApproximate = $isApproximate;
    }

    /**
     * @return mixed
     */
    public function getIsApproximate()
    {
        return $this->isApproximate;
    }

    /**
     * @param mixed $isRange
     */
    public function setIsRange($isRange)
    {
        $this->isRange = $isRange;
    }

    /**
     * @return mixed
     */
    public function getIsRange()
    {
        return $this->isRange;
    }

    /**
     * @param mixed $isRecurring
     */
    public function setIsRecurring($isRecurring)
    {
        $this->isRecurring = $isRecurring;
    }

    /**
     * @return mixed
     */
    public function getIsRecurring()
    {
        return $this->isRecurring;
    }

    /**
     * @param mixed $numRepetitions
     */
    public function setNumRepetitions($numRepetitions)
    {
        $this->numRepetitions = $numRepetitions;
    }

    /**
     * @return mixed
     */
    public function getNumRepetitions()
    {
        return $this->numRepetitions;
    }

    /**
     * @param \Gedcomx\Util\SimpleDate $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return \Gedcomx\Util\SimpleDate
     */
    public function getStart()
    {
        return $this->start;
    }

}
