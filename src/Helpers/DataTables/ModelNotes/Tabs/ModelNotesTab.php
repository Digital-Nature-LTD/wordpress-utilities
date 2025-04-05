<?php

namespace DigitalNature\WordPressUtilities\Helpers\DataTables\ModelNotes\Tabs;

use DigitalNature\WordPressUtilities\Factories\ModelFactory;
use DigitalNature\WordPressUtilities\Helpers\DataTables\ModelNotes\ModelNotesDataTableHelper;
use DigitalNature\WordPressUtilities\Helpers\DataTableTabHelper;
use DigitalNature\WordPressUtilities\Helpers\MessageHelper;
use DigitalNature\WordPressUtilities\Models\Model;
use DigitalNature\WordPressUtilities\Models\ModelNote;
use WP_Post;
use WP_Query;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ModelNotesTab extends DataTableTabHelper
{
    /**
     * @var Model|null
     */
    private ?Model $model = null;

    /**
     *
     */
    public function __construct()
    {
        $modelId = $_GET['model'] ?? null;

        if (!$modelId) {
            MessageHelper::error_and_exit('You must submit a model to view notes. Go back and try again');
        }

        $model = ModelFactory::from_id($modelId);

        if (!$model) {
            MessageHelper::error_and_exit("Could not find model with ID $modelId");
        }

        $this->model = $model;
    }

    /**
     * @return bool
     */
    public function is_searchable(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function is_paginated(): bool
    {
        return true;
    }

    public function get_label(): string
    {
        return 'Notes';
    }

    public function get_slug(): string
    {
        return 'model-notes';
    }

    /**
     * @return array
     */
    public function get_data(): array
    {
        if (empty($this->model)) {
            return [];
        }

        if (ModelNotesDataTableHelper::is_searching_by_id()) {
            $data = $this->get_notes_by_id();
        } else {
            $data = $this->get_notes();
        }

        return $this->format_data_for_table($data);
    }

    /**
     * @return int
     */
    public function get_result_count(): int
    {
        // if searching by ID then it's either 1 or 0, we don't need to count posts
        if (ModelNotesDataTableHelper::is_searching_by_id()) {
            return count($this->get_notes_by_id());
        }

        $query = $this->get_query();

        return $query->found_posts;
    }

    /**
     * Gets records by ID
     *
     * @return array
     */
    protected function get_notes_by_id(): array
    {
        $note = ModelNote::from_id(ModelNotesDataTableHelper::get_active_search_term());

        if (!$note) {
            return [];
        }

        if ($note->model_id !== $this->model->id) {
            // it's from another model, don't return it
            return [];
        }

        return [
            $note->id => $note
        ];
    }

    /**
     * @return array
     */
    protected function get_notes(): array
    {
        $query = $this->get_query();

        return $query->get_posts();
    }

    /**
     * @return WP_Query
     */
    protected function get_query(): WP_Query
    {
        return new WP_Query([
            'posts_per_page' => ModelNotesDataTableHelper::get_active_page_size(),
            'offset' => ModelNotesDataTableHelper::get_active_page_offset(),
            'page' => ModelNotesDataTableHelper::get_active_page_no(),
            'post_type'     => ModelNote::get_post_type(),
            'post_status'   => 'publish',
            'orderby' => [
                'ID' => 'DESC',
                'meta_value_num' => 'DESC',
            ],
            'meta_key' => ModelNote::METADATA_KEY_NOTE_WRITTEN_ON,
            'meta_query' => $this->get_meta_query(),
        ]);
    }

    /**
     * @return array
     */
    protected function get_meta_query(): array
    {
        if (ModelNotesDataTableHelper::is_searching()) {
            $searchTerm = ModelNotesDataTableHelper::get_active_search_term();

            return [
                'relation' => 'AND',
                [
                    'key'   => ModelNote::METADATA_KEY_NOTE,
                    'value' => $searchTerm,
                    'compare' => 'LIKE'
                ],
                [
                    'key'   => ModelNote::METADATA_KEY_MODEL_ID,
                    'value' => $this->model->id,
                    'compare' => '='
                ],
            ];
        }


        return [
            [
                'key'   => ModelNote::METADATA_KEY_MODEL_ID,
                'value' => $this->model->id,
                'compare' => '='
            ],
        ];
    }

    /**
     * Formats the records for use in a data table
     *
     * @param array $records
     * @return array
     */
    protected function format_data_for_table(array $records): array
    {
        $formattedRecords = [];

        foreach ($records as $note) {
            if ($note instanceof WP_Post) {
                $note = ModelNote::from_id($note->ID);
            }

            $date = $note->get_created_datetime();
            $authorName = $note->get_author_name();
            $originalUser = $note->get_original_user();

            if ($originalUser) {
                $authorName = "$originalUser->first_name $originalUser->last_name masquerading as $authorName";
            }

            $formattedRecords[] = [
                'Date' => ($date ? $date->format('d/m/Y H:i:s') : 'unknown'),
                'Note' => $note->get_note(),
                'Author' => $authorName,
            ];
        }

        return $formattedRecords;
    }
}