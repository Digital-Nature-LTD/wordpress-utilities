<?php

namespace DigitalNature\WordPressUtilities\Query\Model;

use DigitalNature\WordPressUtilities\Models\Model;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ModelFromAttributesQuery extends ModelQuery
{
    /**
     * @return array
     */
    protected function get_query(): array
    {
        /** @var Model $model */
        $model = $this->model_class;

        return array_merge(
            [
                'post_type'         => $model::get_post_type(),
                'post_status'       => $this->post_statuses,
                'posts_per_page'    => 1,
                'meta_query'        => $this->get_meta_query(),
            ],
            $this->additional_queries
        );
    }

    /**
     * We retrieve one model
     *
     * @return Model|null
     */
    public function run(): ?Model
    {
        return $this->retrieve_model();
    }
}