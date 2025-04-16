<?php

namespace DigitalNature\WordPressUtilities\Query\ModelNote;

use DigitalNature\WordPressUtilities\Models\Model;
use DigitalNature\WordPressUtilities\Models\ModelNote;
use DigitalNature\WordPressUtilities\Query\BaseModelQuery;
use WP_Post;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class ModelNoteQuery extends BaseModelQuery
{
    protected Model $model;

    /**
     * @return string
     */
    protected function get_cache_key(): string
    {
        $cacheParams = [];

        if (!empty($this->model)) {
            $cacheParams[] = $this->model->id;
        }

        return ModelNote::cache_key_name(get_called_class(), $cacheParams);
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function set_model(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return ModelNote[]|null
     */
    protected function get_cached_result(): ?array
    {
        return ModelNote::cache_retrieve_multiple($this->get_cache_key(), false);
    }

    /**
     * @param ModelNote[] $models
     * @return void
     */
    protected function set_cached_result(array $models): void
    {
        ModelNote::cache_set($this->get_cache_key(), $models);
    }

    /**
     * @param WP_Post $post
     * @return ModelNote|null
     */
    protected function hydrate_model(WP_Post $post): ?ModelNote
    {
        return ModelNote::from_post($post);
    }
}