<?php

namespace library\task\Cron;

use OutOfRangeException;

/**
 * Abstract CRON expression field
 */
abstract class AbstractField implements FieldInterface
{
    /**
     * Full range of values that are allowed for this field type
     * @var array
     */
    protected $fullRange = [];

    /**
     * Literal values we need to convert to integers
     * @var array
     */
    protected $literals = [];

    /**
     * Start value of the full range
     * @var integer
     */
    protected $rangeStart;

    /**
     * End value of the full range
     * @var integer
     */
    protected $rangeEnd;


    public function __construct()
    {
        $this->fullRange = range($this->rangeStart, $this->rangeEnd);
    }

    /**
     * Check to see if a field is satisfied by a value
     *
     * @param string $dateValue Date value to check
     * @param string $value Value to test
     *
     * @return bool
     */
    public function isSatisfied($dateValue, $value)
    {
        if ($this->isIncrementsOfRanges($value)) {
            return $this->isInIncrementsOfRanges($dateValue, $value);
        } elseif ($this->isRange($value)) {
            return $this->isInRange($dateValue, $value);
        }

        return $value == '*' || $dateValue == $value;
    }

    /**
     * Check if a value is a range
     *
     * @param string $value Value to test
     *
     * @return bool
     */
    public function isRange($value)
    {
        return strpos($value, '-') !== false;
    }

    /**
     * Check if a value is an increments of ranges
     *
     * @param string $value Value to test
     *
     * @return bool
     */
    public function isIncrementsOfRanges($value)
    {
        return strpos($value, '/') !== false;
    }

    /**
     * Test if a value is within a range
     *
     * @param string $dateValue Set date value
     * @param string $value Value to test
     *
     * @return bool
     */
    public function isInRange($dateValue, $value)
    {
        $parts = array_map('trim', explode('-', $value, 2));

        return $dateValue >= $parts[0] && $dateValue <= $parts[1];
    }

    /**
     * Test if a value is within an increments of ranges (offset[-to]/step size)
     *
     * @param string $dateValue Set date value
     * @param string $value Value to test
     *
     * @return bool
     */
    public function isInIncrementsOfRanges($dateValue, $value)
    {
        $chunks = array_map('trim', explode('/', $value, 2));
        $range = $chunks[0];
        $step = isset($chunks[1]) ? $chunks[1] : 0;

        // No step or 0 steps aren't cool
        if (is_null($step) || '0' === $step || 0 === $step) {
            return false;
        }

        // Expand the * to a full range
        if ('*' == $range) {
            $range = $this->rangeStart . '-' . $this->rangeEnd;
        }

        // Generate the requested small range
        $rangeChunks = explode('-', $range, 2);
        $rangeStart = $rangeChunks[0];
        $rangeEnd = isset($rangeChunks[1]) ? $rangeChunks[1] : $rangeStart;

        if ($rangeStart < $this->rangeStart || $rangeStart > $this->rangeEnd || $rangeStart > $rangeEnd) {
            throw new OutOfRangeException('Invalid range start requested');
        }

        if ($rangeEnd < $this->rangeStart || $rangeEnd > $this->rangeEnd || $rangeEnd < $rangeStart) {
            throw new OutOfRangeException('Invalid range end requested');
        }

        if ($step > ($rangeEnd - $rangeStart) + 1) {
            throw new OutOfRangeException('Step cannot be greater than total range');
        }

        $thisRange = range($rangeStart, $rangeEnd, $step);

        return in_array($dateValue, $thisRange);
    }

    /**
     * Returns a range of values for the given cron expression
     *
     * @param string $expression The expression to evaluate
     * @param int $max Maximum offset for range
     *
     * @return array
     */
    public function getRangeForExpression($expression, $max)
    {
        $values = array();

        if ($this->isRange($expression) || $this->isIncrementsOfRanges($expression)) {
            if (!$this->isIncrementsOfRanges($expression)) {
                list ($offset, $to) = explode('-', $expression);
                $stepSize = 1;
            } else {
                $range = array_map('trim', explode('/', $expression, 2));
                $stepSize = isset($range[1]) ? $range[1] : 0;
                $range = $range[0];
                $range = explode('-', $range, 2);
                $offset = $range[0];
                $to = isset($range[1]) ? $range[1] : $max;
            }
            $offset = $offset == '*' ? 0 : $offset;
            for ($i = $offset; $i <= $to; $i += $stepSize) {
                $values[] = $i;
            }
            sort($values);
        } else {
            $values = array($expression);
        }

        return $values;
    }

    protected function convertLiterals($value)
    {
        if (count($this->literals)) {
            $key = array_search($value, $this->literals);
            if ($key !== false) {
                return $key;
            }
        }

        return $value;
    }

    /**
     * Checks to see if a value is valid for the field
     *
     * @param string $value
     * @return bool
     */
    public function validate($value)
    {
        $value = $this->convertLiterals($value);

        // All fields allow * as a valid value
        if ('*' === $value) {
            return true;
        }

        // You cannot have a range and a list at the same time
        if (strpos($value, ',') !== false && strpos($value, '-') !== false) {
            return false;
        }

        if (strpos($value, '/') !== false) {
            list($range, $step) = explode('/', $value);
            return $this->validate($range) && filter_var($step, FILTER_VALIDATE_INT);
        }

        if (strpos($value, '-') !== false) {
            if (substr_count($value, '-') > 1) {
                return false;
            }

            $chunks = explode('-', $value);
            $chunks[0] = $this->convertLiterals($chunks[0]);
            $chunks[1] = $this->convertLiterals($chunks[1]);

            if ('*' == $chunks[0] || '*' == $chunks[1]) {
                return false;
            }

            return $this->validate($chunks[0]) && $this->validate($chunks[1]);
        }

        // Validate each chunk of a list individually
        if (strpos($value, ',') !== false) {
            foreach (explode(',', $value) as $listItem) {
                if (!$this->validate($listItem)) {
                    return false;
                }
            }
            return true;
        }

        // We should have a numeric by now, so coerce this into an integer
        if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
            $value = (int)$value;
        }

        return in_array($value, $this->fullRange, true);
    }
}
