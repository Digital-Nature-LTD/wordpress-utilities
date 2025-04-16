<?php

namespace DigitalNature\WordPressUtilities\Query\UserNote;

use DigitalNature\WordPressUtilities\Models\UserNote;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class UserNoteFromUserQuery extends UserNoteQuery
{
    /**
     * @return array
     */
    protected function get_query(): array
    {
        return [
            'post_type' => UserNote::get_post_type(),
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => UserNote::METADATA_KEY_NOTE_USER_ID,
                    'value' => $this->user->ID,
                    'compare' => '='
                ],
            ],
            'orderby' => [
                'ID' => 'DESC',
                'meta_value_num' => 'DESC',
            ],
            'meta_key' => UserNote::METADATA_KEY_NOTE_WRITTEN_ON,
        ];
    }

    /**
     * We retrieve multiple models
     *
     * @return UserNote[]
     */
    public function run(): array
    {
        return $this->retrieve_models();
    }
}