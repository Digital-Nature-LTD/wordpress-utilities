<?php

namespace DigitalNature\WordPressUtilities\Helpers;

use Exception;
use WP_Query;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class DataTableTabMonthlyHelper extends DataTableTabHelper
{
    abstract public function get_date_property(): string;
    abstract public function get_data_table_helper_class(): string;

    /**
     * @param array $additionalArgs
     * @param array $additionalMetaQuery
     * @return WP_Query
     * @throws Exception
     */
    protected function build_query(array $additionalArgs = [], array $additionalMetaQuery = []): WP_Query
    {
        $defaultArgs = [
            'posts_per_page' => '-1',
            'meta_query' => $this->build_meta_query($additionalMetaQuery),
        ];

        return new WP_Query(array_merge($defaultArgs, $additionalArgs));
    }

    /**
     * @param array $additionalMetaQuery
     * @return array
     * @throws Exception
     */
    protected function build_meta_query(array $additionalMetaQuery = []): array
    {
        $dataTableHelper = $this->get_data_table_helper_class();

        if (!is_subclass_of($dataTableHelper, DataTableMonthlyHelper::class)) {
            throw new Exception("This tab uses a data table helper that doesn't support monthly pagination");
        }

        $defaultMetaQuery = [
            'relation' => 'AND',
            [
                'key'   => $this->get_date_property(),
                'value' => $dataTableHelper::get_selected_month_start_datetime()->getTimestamp(),
                'compare' => '>='
            ],
            [
                'key'   => $this->get_date_property(),
                'value' => $dataTableHelper::get_selected_month_end_datetime()->getTimestamp(),
                'compare' => '<='
            ],
        ];

        return array_merge($defaultMetaQuery, $additionalMetaQuery);
    }
}