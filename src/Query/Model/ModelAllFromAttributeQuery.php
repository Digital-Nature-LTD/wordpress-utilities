<?php

namespace DigitalNature\WordPressUtilities\Query\Model;

use DigitalNature\WordPressUtilities\Models\Model;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ModelAllFromAttributeQuery extends ModelQuery
{
    /**
     * @return array
     */
    protected function get_query(): array
    {
        /** @var Model $model */
        $model = $this->model_class;

        return [
            'post_type'         => $model::get_post_type(),
            'post_status'       => $this->post_statuses,
            'posts_per_page'    => $this->posts_per_page,
            'offset'            => $this->offset,
            'meta_query'        => $this->get_meta_query(),
        ];
    }

    /**
     * We retrieve multiple models
     *
     * @return Model[]
     */
    public function run(): array
    {
        return $this->retrieve_models();
    }
}