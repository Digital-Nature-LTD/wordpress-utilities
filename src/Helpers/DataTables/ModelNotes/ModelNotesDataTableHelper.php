<?php

namespace DigitalNature\WordPressUtilities\Helpers\DataTables\ModelNotes;

use DigitalNature\WordPressUtilities\Admin\Menu;
use DigitalNature\WordPressUtilities\Helpers\DataTableHelper;
use DigitalNature\WordPressUtilities\Helpers\DataTables\ModelNotes\Tabs\ModelNotesTab;
use DigitalNature\WordPressUtilities\Helpers\DataTableTabHelper;

class ModelNotesDataTableHelper extends DataTableHelper
{
    const DEFAULT_PAGE_SIZE = 100;

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
            self::get_individual_cache_key(static::get_model_id_key()),
        ];

        return implode('_' , $keySegments);
    }

    /**
     * @return string
     */
    public static function get_model_id_key(): string
    {
        return 'model';
    }

    public static function get_base_cache_key(): string
    {
        return 'model_notes_data';
    }

    /**
     * @return string
     */
    public static function get_base_url(): string
    {
        return 'page=' . Menu::MODEL_NOTES_URL;
    }

    /**
     * @return DataTableTabHelper[]
     */
    public static function get_tab_classes(): array
    {
        return [
            new ModelNotesTab(),
        ];
    }

    /**
     * @return string
     */
    public static function get_default_tab(): string
    {
        $tab = new ModelNotesTab();

        return $tab->get_slug();
    }
}