<?php

namespace DigitalNature\WordPressUtilities\Helpers;

use DateTime;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class DateHelper
{
    /**
     * @param DateTime|null $aDateInTheMonth
     * @return DateTime
     */
    public static function get_start_of_month(DateTime $aDateInTheMonth = null): DateTime
    {
        $date = $aDateInTheMonth ?? new DateTime();
        $date->modify('first day of this month')->setTime(0,0,0);
        return $date;
    }

    /**
     * @param DateTime|null $aDateInTheMonth
     * @return DateTime
     */
    public static function get_end_of_month(DateTime $aDateInTheMonth = null): DateTime
    {
        $date = $aDateInTheMonth ?? new DateTime();
        $date->modify('last day of this month')->setTime(23, 59, 59);
        return $date;
    }
}
