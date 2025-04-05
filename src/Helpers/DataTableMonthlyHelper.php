<?php

namespace DigitalNature\WordPressUtilities\Helpers;

use DateTime;
use DigitalNature\WordPressUtilities\WordPressUtilitiesConfig;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class DataTableMonthlyHelper extends DataTableHelper
{
    /**
     * @return int
     */
    public static function get_no_of_months_to_go_back(): int
    {
        // by default, we go back 1 year
        return 12;
    }

    public static function get_pagination_links(DataTableTabHelper $tab): array
    {
        $currentPage = static::get_active_page_no();
        $currentDate = new DateTime();
        $links = [];
        $format = 'm/Y';
        $totalPages = static::get_no_of_months_to_go_back();

        // add the 'first' links
        if (static::get_active_page_no() !== 1) {
            $links[] = ['no' => 1, 'content' => '<span class="dashicons dashicons-controls-skipback"></span>'];
            $links[] = ['no' => $currentPage-1, 'content' => '<span class="dashicons dashicons-controls-back"></span>'];
        }

        // adds the pages prior to this one
        for ($i = $currentPage-2; $i < $currentPage; $i++) {
            if ($i < 1) {
                continue;
            }

            $offset = $i-1;
            $thisPageDate = clone $currentDate;
            $thisPageDate->modify("-$offset month");

            $links[] = ['no' => $i, 'content' => $thisPageDate->format($format)];
        }




        // add the current page
        $offset = $currentPage-1;
        $thisPageDate = clone $currentDate;
        $thisPageDate->modify("-$offset month");

        // add the current page
        $links[] = ['no' => $currentPage, 'content' => $thisPageDate->format($format), 'current' => true];





        // add the pages following this one
        for ($i = $currentPage+1; $i < $currentPage+3; $i++) {
            if ($i > $totalPages) {
                continue;
            }

            $offset = $i-1;
            $thisPageDate = clone $currentDate;
            $thisPageDate->modify("-$offset month");

            $links[] = ['no' => $i, 'content' => $thisPageDate->format($format)];
        }

        if (static::get_active_page_no() != $totalPages) {
            $links[] = ['no' => $currentPage+1, 'content' => '<span class="dashicons dashicons-controls-forward"></span>'];
            $links[] = ['no' => $totalPages, 'content' => '<span class="dashicons dashicons-controls-skipforward"></span>'];
        }

        return $links;
    }

    /**
     * @return string
     */
    public static function render_pagination(): string
    {
        $tab = static::get_active_tab_object();

        if (!$tab || !$tab->is_paginated()) {
            return '';
        }

        ob_start();

	    TemplateHelper::render(
		    'digital-nature-wordpress-utilities/admin/data-table/pagination.php',
		    [
			    'helperClass' => static::class,
		    ],
		    trailingslashit(WordPressUtilitiesConfig::get_plugin_dir() . '/templates')
	    );

        $message = ob_get_contents();

        ob_end_clean();

        return $message;
    }

    /**
     * @return DateTime
     */
    public static function get_selected_month_start_datetime(): DateTime
    {
        $currentMonth = static::get_selected_month_datetime();

        return DateHelper::get_start_of_month($currentMonth);
    }

    /**
     * @return DateTime
     */
    public static function get_selected_month_end_datetime(): DateTime
    {
        $currentMonth = static::get_selected_month_datetime();

        return DateHelper::get_end_of_month($currentMonth);
    }

    /**
     * @return DateTime
     */
    public static function get_selected_month_datetime(): DateTime
    {
        $date = new DateTime();
        $currentPage = static::get_active_page_no();

        // 1 is the current month, so we offset by taking that away
        $monthlyOffset = $currentPage - 1;

        $date->modify("-$monthlyOffset month");

        return $date;
    }
}
