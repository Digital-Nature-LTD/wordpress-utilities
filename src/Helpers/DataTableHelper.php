<?php

namespace DigitalNature\WordPressUtilities\Helpers;

use DigitalNature\WordPressUtilities\Admin\Menu;
use DigitalNature\WordPressUtilities\WordPressUtilitiesConfig;
use Exception;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class DataTableHelper
{
    const DEFAULT_PAGE_NO = 1;
    const DEFAULT_PAGE_SIZE = 20;

    abstract public static function get_base_url(): string;

    /**
     * @return DataTableTabHelper[]
     */
    abstract public static function get_tab_classes(): array;

    abstract public static function get_default_tab(): string;

    abstract public static function get_base_cache_key(): string;

    /**
     * @return string[]
     */
    public static function get_tabs(): array
    {
        $classes = static::get_tab_classes();

        $tabs = [];

        foreach($classes as $class) {
            $tabs[$class->get_slug()] = $class;
        }

        return $tabs;
    }

    /**
     * @param string $paramName
     * @return void
     */
    public static function get_individual_cache_key(string $paramName): ?string
    {
        $value = $_GET[$paramName] ?? null;

        if (empty($value)) {
            return null;
        }

        return sanitize_title($value);
    }

    /**
     * @return string
     */
    public static function get_cache_key(): string
    {
        $keySegments = [
            static::get_base_cache_key(),
            self::get_individual_cache_key(static::get_active_search_key()),
            self::get_individual_cache_key(static::get_active_page_no_key()),
            self::get_individual_cache_key(static::get_active_page_size_key()),
            self::get_individual_cache_key(static::get_active_tab_key()),
        ];

        return implode('_' , $keySegments);
    }

    /**
     * @return string
     */
    public static function get_cache_flush_url(): string
    {
        return '/wp-admin/admin.php?page=' . Menu::CACHE_FLUSH_URL . '&key=' . static::get_cache_key();
    }

    /**
     * @return string
     */
    public static function get_active_tab(): string
    {
        return $_GET[static::get_active_tab_key()] ?? static::get_default_tab();
    }

    /**
     * @return DataTableTabHelper|null
     */
    public static function get_active_tab_object(): ?DataTableTabHelper
    {
        $tabKey = static::get_active_tab();
        $tabs = static::get_tabs();

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $tabs[$tabKey] ?? null;
    }

    /**
     * @return array
     */
    public static function get_active_tab_data(): array
    {
        $tab = static::get_active_tab_object();

        if (!$tab) {
            return [];
        }

        $cacheKey = static::get_cache_key();

        // check the cache
        $data = get_transient($cacheKey);

        if (!$data) {
            // data was not in cache, retrieve and cache it
            try {
                $data = $tab->get_data();
                set_transient($cacheKey, $data, 600);
            } catch (Exception $e) {
                LogHelper::write("DATA_TABLE_HELPER: Could not get tab data, an exception was thrown: {$e->getMessage()}");
                return [];
            }
        }

        return $data;
    }

    /**
     * @return string|null
     */
    public static function get_active_search_term(): ?string
    {
        return $_GET[static::get_active_search_key()] ?? null;
    }

    /**
     * @return int
     */
    public static function get_active_page_no(): int
    {
        return $_GET[static::get_active_page_no_key()] ?? static::DEFAULT_PAGE_NO;
    }

    /**
     * @return int
     */
    public static function get_active_page_offset(): int
    {
        return (static::get_active_page_no() - 1) * static::get_active_page_size();
    }

    /**
     * @return int
     */
    public static function get_active_page_size(): int
    {
        return $_GET[static::get_active_page_size_key()] ?? static::DEFAULT_PAGE_SIZE;
    }

    /**
     * @return string
     */
    public static function get_active_tab_key(): string
    {
        return 'tab';
    }

    /**
     * @return string
     */
    public static function get_active_page_no_key(): string
    {
        return 'page-no';
    }

    /**
     * @return string
     */
    public static function get_active_page_size_key(): string
    {
        return 'page-size';
    }

    /**
     * @return string
     */
    public static function get_active_search_key(): string
    {
        return 'search';
    }

    /**
     * @return string
     */
    public static function get_search_label(): string
    {
        return 'Search:';
    }

    /**
     * @return string
     */
    public static function get_search_submit_label(): string
    {
        return 'Search';
    }

    /**
     * @return array
     */
    public static function get_url_params_to_preserve_across_tabs(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public static function get_url_params(): string
    {
        $values = [];

        foreach (static::get_url_params_to_preserve_across_tabs() as $param) {
            if (isset($_GET[$param])) {
                $values[] = "$param={$_GET[$param]}";
            }
        }

        return implode('&', $values);
    }

    /**
     * @return string
     */
    public static function get_base_url_with_params(): string
    {
        $baseUrl = static::get_base_url();
        $params = static::get_url_params();

        return empty($params) ? $baseUrl : "$baseUrl&$params";
    }

    /**
     * @return bool
     */
    public static function is_searching_by_id(): bool
    {
        return static::is_searching() && is_int(filter_var(static::get_active_search_term(), FILTER_VALIDATE_INT));
    }

    /**
     * @return bool
     */
    public static function is_searching(): bool
    {
        return static::get_active_search_term() !== null;
    }

    public static function get_pagination_links(DataTableTabHelper $tab): array
    {
        $links = [];

        $currentPage = static::get_active_page_no();

        try {
            $totalResults = $tab->get_result_count();
        } catch (Exception $e) {
            LogHelper::write("DATA_TABLE_HELPER: Could not get tab result count, an exception was thrown: {$e->getMessage()}");
            return [];
        }

        if ($totalResults <= static::get_active_page_size()) {
            // we have fewer results than fit on a page, pagination not needed
            return $links;
        }

        // work out the full number of pages
        $totalPages = (int) ceil($totalResults / static::get_active_page_size());

        if (!$totalPages) {
            // no pages?? weird... don't show anything
            return $links;
        }

        // add the 'first' links
        if (static::get_active_page_no() !== 1) {
            $links[] = ['no' => 1, 'content' => '<span class="dashicons dashicons-controls-skipback"></span>'];
            $links[] = ['no' => $currentPage-1, 'content' => '<span class="dashicons dashicons-controls-back"></span>'];
        }

        for ($i = $currentPage-2; $i < $currentPage; $i++) {
            if ($i < 1) {
                continue;
            }

            $links[] = ['no' => $i];
        }

        $links[] = ['no' => $currentPage, 'current' => true];

        for ($i = $currentPage+1; $i < $currentPage+3; $i++) {
            if ($i > $totalPages) {
                continue;
            }

            $links[] = ['no' => $i];
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
    public static function render_tab_intro(): string
    {
        $tab = static::get_active_tab_object();

        if (!$tab) {
            return '';
        }

        return $tab->get_intro_text();
    }

    /**
     * @return string
     */
    public static function render_tabs(): string
    {
        if (count(static::get_tabs()) < 2) {
            return '';
        }

        ob_start();

	    TemplateHelper::render(
		    'digital-nature-wordpress-utilities/admin/data-table/tabs.php',
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
     * @return string
     */
    public static function render_search(): string
    {
        $tab = static::get_active_tab_object();

        if (!$tab || !$tab->is_searchable()) {
            return '';
        }

        ob_start();

	    TemplateHelper::render(
		    'digital-nature-wordpress-utilities/admin/data-table/search.php',
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



    public static function render_table(): string
    {
        ob_start();

	    TemplateHelper::render(
		    'digital-nature-wordpress-utilities/admin/data-table/table.php',
		    [
			    'helperClass' => static::class,
		    ],
		    trailingslashit(WordPressUtilitiesConfig::get_plugin_dir() . '/templates')
	    );

        $message = ob_get_contents();

        ob_end_clean();

        return $message;
    }
}
