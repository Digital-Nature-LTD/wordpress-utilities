<?php

namespace DigitalNature\WordPressUtilities\Query;

use DigitalNature\WordPressUtilities\Helpers\LogHelper;
use DigitalNature\WordPressUtilities\Models\Model;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class BaseModelQuery
{
    /**
     * @return array
     */
    protected abstract function get_query(): array;

    /**
     * @return string
     */
    protected abstract function get_cache_key(): string;

    /**
     * @return Model|Model[]|null
     */
    protected abstract function get_cached_result();

    /**
     * @param Model[] $models
     * @return void
     */
    protected abstract function set_cached_result(array $models): void;

    /**
     * @return Model|Model[]
     */
    public abstract function run();

    /**
     * Flushes the cache for this query
     *
     * @return void
     */
    public function flush(): void
    {
        $cacheName = $this->get_cache_key();
        LogHelper::write("MODEL_QUERY: $cacheName Deleting cache");
        delete_transient($cacheName);
    }

    /**
     * Retrieves an array of models
     *
     * @return Model[]
     */
    protected function retrieve_models(): array
    {
        $cached = $this->get_cached_result();

        if ($cached) {
            return $cached;
        }

        $query = $this->get_query();

        $posts = get_posts($query);

        $models = [];

        foreach ($posts as $post) {
            $model = $this->hydrate_model($post);

            if ($model) {
                $models[] = $model;
            }
        }

        if (!empty($models)) {
            // cache the result
            $this->set_cached_result($models);
        }

        return $models;
    }

    /**
     * Retrieves a single model
     *
     * @return Model|null
     */
    protected function retrieve_model(): ?Model
    {
        $models = $this->retrieve_models();

        if (empty($models)) {
            return null;
        }

        return end($models);
    }
}