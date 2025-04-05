<?php

namespace DigitalNature\WordPressUtilities\Query\Model;

use DigitalNature\WordPressUtilities\Query\BaseModelQuery;
use DigitalNature\WordPressUtilities\Models\Model;
use WP_Post;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class ModelQuery extends BaseModelQuery
{
    protected string $model_class;
    protected string $meta_query_relation = 'AND';
    protected array $meta_query = [];
    protected int $posts_per_page = 1;
    protected int $offset = 0;
    protected array $additional_queries = [];
    protected array $post_statuses;

    
    /**
     * @return string
     */
    protected function get_cache_key(): string
    {
        // the attributes and operands could be anything, we will just serialise the query and hash it for the cache key
        $cacheString = md5(serialize($this->get_query()));

        return Model::cache_key_name(get_called_class(), [$cacheString]);
    }

    /**
     * @return array
     */
    public function get_meta_query(): array
    {
        // if anything has a string key then we need to pull it out separately to ensure we don't have issues with the ... operator
        $metaQuery = [];
        $metaQueryKeyValues = [];
        foreach (array_keys($this->meta_query) as $key) {
            if (is_numeric($key)) {
                // numeric keys can be added as normal
                $metaQuery[] = $this->meta_query[$key];
                continue;
            }

            // string keys need adding separately
            $metaQueryKeyValues[$key] = $this->meta_query[$key];
        }

        return array_merge(
            [
                'relation' => $this->meta_query_relation,
                ...$metaQuery
            ],
            $metaQueryKeyValues
        );
    }





    /**
     * @param string $modelClass
     * @return $this
     */
    public function set_model(string $modelClass): self
    {
        $this->model_class = $modelClass;

        return $this;
    }

    /**
     * @param string $relation
     * @return $this
     */
    public function set_relation_for_meta_query(string $relation): self
    {
        $this->meta_query_relation = $relation;

        return $this;
    }

    /**
     * @param array $postStatuses
     * @return $this
     */
    public function set_post_statuses(array $postStatuses): self
    {
        $this->post_statuses = $postStatuses;

        return $this;
    }

    /**
     * @param int $postsPerPage
     * @return $this
     */
    public function set_posts_per_page(int $postsPerPage): self
    {
        $this->posts_per_page = $postsPerPage;

        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function set_offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param string $comparison
     * @return $this
     */
    public function add_attribute_value_to_meta_query(string $attribute, string $value, string $comparison = '='): self
    {
        $this->meta_query[] = [
            'key' => $attribute,
            'value' => $value,
            'compare' => $comparison
        ];

        return $this;
    }

    /**
     * @param array $additionalMetaQueries
     * @return $this
     */
    public function add_to_meta_query(array $additionalMetaQueries): self
    {
        $this->meta_query = array_merge($this->meta_query, $additionalMetaQueries);

        return $this;
    }

    /**
     * @param array $additionalQueries
     * @return self
     */
    public function set_additional_queries(array $additionalQueries): self
    {
        $this->additional_queries = $additionalQueries;

        return $this;
    }





    /**
     * @return Model[]|null
     */
    protected function get_cached_result(): ?array
    {
        /** @var Model $model */
        $model = $this->model_class;

        return $model::cache_retrieve_multiple($this->get_cache_key(), false);
    }

    /**
     * @param Model[] $models
     * @return void
     */
    protected function set_cached_result(array $models): void
    {
        /** @var Model $model */
        $model = $this->model_class;

        // we only store these for 5 minutes as we are not going to implement any specific cache clearing in most cases
        $model::cache_set($this->get_cache_key(), $models, 300);
    }

    /**
     * @param WP_Post $post
     * @return Model|null
     */
    protected function hydrate_model(WP_Post $post): ?Model
    {
        /** @var Model $model */
        $model = $this->model_class;
        return $model::from_post($post);
    }
}