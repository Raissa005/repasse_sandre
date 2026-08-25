<?php

namespace RR\libs;

use DateInterval;
use DateTime;

class Date
{
    public static function date($date)
    {
        return date("d/m/Y", strtotime($date));
    }

    public static function day($date)
    {
        return date("d", strtotime($date));
    }

    public static function date_full($date)
    {
        $objeto_data = date_create($date);

        return str_replace(' ', ' de ', date_format($objeto_data, "d F Y"));
    }

    public static function date_hour($date)
    {
        return date("d/m/Y H:i", strtotime($date));
    }

    public static function date_hour_full($date)
    {
        return strftime("%d de %B de %Y às %H:%M", strtotime($date));
    }

    public static function month_full($date)
    {
        $dateTime = new DateTime($date);
        return $dateTime->format('F');
    }

    public static function month_abreviation($date)
    {
        return strftime("%b", strtotime($date));
    }

    public static function hour($date)
    {
        return date("H:i", strtotime($date));
    }

    public static function year_month($date)
    {
        return date("Y-m", strtotime($date));
    }

    public static function year($date)
    {
        return date("Y", strtotime($date));
    }

    public static function month($date)
    {
        return date("m", strtotime($date));
    }

    public static function rangeOfDays($dateStart, $dateEnd)
    {
        $dateStart = new DateTime($dateStart);
        $dateEnd = new DateTime($dateEnd);

        // Rescues difference between dates.
        $dateInterval = $dateStart->diff($dateEnd);
        return $dateInterval->days;
    }

    /**
     * Adds the days spent ignoring Saturdays and Sundays,
     * it still does not consider holidays to do this you need
     * to register a table of holidays or query holidays from an external api 
     */
    public static function add_working_days($date, $day)
    {
        if (!($date instanceof \DateTime) || is_string($date)) {
            $date = new \DateTime($date);
        }

        if ($date instanceof \DateTime) {
            $newDate = clone $date;
        }

        if ($day == 0) {
            return $newDate->format('Y-m-d');
        }

        $i = 1;

        while ($i <= abs($day)) {

            $newDate->modify(($day > 0 ? ' +' : ' -') . '1 day');

            $next_day_number = $newDate->format('N');

            if (!in_array($next_day_number, [6, 7])) {
                $i++;
            }
        }

        return $newDate->format('Y-m-d');
    }

    /**
     * Create date array with all dates within a range.
     */
    public static function createArrayDates($dateStart, $dateEnd)
    {
        // Create empty date array.
        $arrDates = [];

        // Modifies the date string into an object.
        $dateStart = new DateTime($dateStart);
        $dateEnd = new DateTime($dateEnd);

        /** If the start date is less than or equal to the end date,
         * it adds the date and adds one more day to the start date.
         */
        while ($dateStart <= $dateEnd) {
            $arrDates[] = $dateStart->format('d/m/Y');
            $dateStart->add(new DateInterval('P1D')); // Add 1 day
        }

        return $arrDates;
    }

    /**
     * Sum the months of a month.
     */
    public static function safeGenNextDueDate(string $isoDateString, $targetDay): string
    {
        // Convert the date to a DateTime object
        $datetime = new DateTime($isoDateString);

        // Get the month before the date modification
        $monthValidationAfter = $datetime->format('m');

        // Modify the date by adding another month
        $datetime->modify("+1 month");

        // Set the target day
        $y = $datetime->format('Y');
        $m = $datetime->format('m');
        $datetime->setDate($y, $m, $targetDay);

        // Get Month after date modification
        $monthValidationBefore = $datetime->format('m');

        // Compares whether the month after the modification was longer than expected.
        $monthDiff = ($monthValidationBefore - $monthValidationAfter);
        if ($monthDiff > 1) {
            // If passed, decreases one month and gets the last day of the month.
            $newDate = $datetime->modify('first day of this month')->modify("-" . ($monthDiff - 1) . " month")->format("Y-m-t");
        } else {
            // If not pass just the string.
            $newDate = $datetime->format('Y-m-d');
        }

        return $newDate;
    }
}
