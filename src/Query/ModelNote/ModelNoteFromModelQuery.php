<?php

namespace DigitalNature\WordPressUtilities\Query\ModelNote;

use DigitalNature\WordPressUtilities\Models\ModelNote;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ModelNoteFromModelQuery extends ModelNoteQuery
{
    /**
     * @return array
     */
    protected function get_query(): array
    {
        return [
            'post_type' => ModelNote::get_post_type(),
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => ModelNote::METADATA_KEY_MODEL_ID,
                    'value' => $this->model->id,
                    'compare' => '='
                ],
            ],
            'orderby' => [
                'ID' => 'DESC',
                'meta_value_num' => 'DESC',
            ],
            'meta_key' => ModelNote::METADATA_KEY_NOTE_WRITTEN_ON,
        ];
    }

    /**
     * We retrieve multiple models
     *
     * @return ModelNote[]
     */
    public function run(): array
    {
        return $this->retrieve_models();
    }
}