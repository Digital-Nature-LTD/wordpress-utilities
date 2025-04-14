<?php

namespace DigitalNature\WordPressUtilities\Models;

use DateTime;
use DigitalNature\WordPressUtilities\Helpers\LogHelper;
use DigitalNature\WordPressUtilities\Query\Model\ModelAllFromAttributeQuery;
use DigitalNature\WordPressUtilities\Query\Model\ModelAllFromAttributesQuery;
use DigitalNature\WordPressUtilities\Query\Model\ModelAllQuery;
use DigitalNature\WordPressUtilities\Query\Model\ModelFromAttributeQuery;
use DigitalNature\WordPressUtilities\Query\Model\ModelFromAttributesQuery;
use DigitalNature\WordPressUtilities\Stores\ModelStore;
use DigitalNature\WordPressUtilities\Traits\CacheableModelTrait;
use Exception;
use WP_Post;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class Model
{
    use CacheableModelTrait;

    const STATUS_PUBLISH = 'publish';
    const STATUS_EXPIRED = 'expired';
    const STATUS_ARCHIVED = 'archived';

    public int $id;
    protected WP_Post $post;

    public static abstract function get_metadata_attribute_maps(): array;
    public static abstract function get_post_type(): string;
    public abstract function get_metadata(): array;

    // constructor cannot have any params
    public function __construct() {}

    /**
     * Creates an empty post for the given model
     * @return static|null
     */
    public static function create(): ?self
    {
        // create the new journey
        $modelData = [
            'post_status'   => self::STATUS_PUBLISH,
            'post_author'   => 1,
            'post_type'     => static::get_post_type()
        ];

        $postId = wp_insert_post($modelData, true);

        if (is_wp_error($postId)) {
            LogHelper::write('Error when creating model with data ' . serialize($modelData) . '. Error Message: ' . $postId->get_error_message());
            return null;
        }

        return static::from_id($postId);
    }

    /**
     * @return void
     */
    public function delete()
    {
        if (!isset($this->id)) {
            return;
        }

        wp_delete_post($this->id, true);
    }

    /**
     * @return void
     */
    public function set_status_expired()
    {
        if (!isset($this->id)) {
            return;
        }

        wp_update_post([
            'ID'            => $this->id,
            'post_status'   => self::STATUS_EXPIRED
        ]);
    }

    /**
     * @return void
     */
    public function set_status_archived()
    {
        if (!isset($this->id)) {
            return;
        }

        wp_update_post([
            'ID'            => $this->id,
            'post_status'   => self::STATUS_ARCHIVED
        ]);
    }

    /**
     * @return void
     */
    public function set_status_published()
    {
        if (!isset($this->id)) {
            return;
        }

        wp_update_post([
            'ID'            => $this->id,
            'post_status'   => self::STATUS_PUBLISH
        ]);
    }

    /**
     * @param WP_Post $post
     * @return static
     */
    public static function from_post(WP_Post $post): Model
    {
        $model = new static();
        $model->set_attributes($post);

        return $model;
    }

    /**
     * @param array $posts
     * @return static[]
     */
    public static function from_posts(array $posts): array
    {
        $models = [];

        foreach($posts as $post) {
            $model = new static();
            $model->set_attributes($post);

            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param int $modelId
     * @return static|null
     */
    public static function from_id(int $modelId): ?self
    {
        try {
            return ModelStore::load($modelId, static::class);
        } catch (Exception $e) {
            LogHelper::write("Could not load model $modelId of type " . static::class);
        }

        return null;
    }

    /**
     * @param string $attribute
     * @param $value
     * @param array $postStatuses
     * @param bool $flushCache
     * @return static|null
     */
    public static function from_attribute(string $attribute, $value, array $postStatuses = [self::STATUS_PUBLISH], bool $flushCache = false): ?self
    {
        $query = new ModelFromAttributeQuery();
        $query->set_model(static::class)->add_attribute_value_to_meta_query($attribute, (string) $value)->set_post_statuses($postStatuses);

        if ($flushCache) {
            $query->flush();
        }

        return $query->run();
    }

    /**
     * @param array $attributeValuePairs
     * @param array $additionalMetaQueries
     * @param array $additionalQueries
     * @param array $postStatuses
     * @param bool $flushCache
     * @return static|null
     */
    public static function from_attributes(array $attributeValuePairs, array $additionalMetaQueries = [], array $additionalQueries = [], array $postStatuses = [self::STATUS_PUBLISH], bool $flushCache = false): ?self
    {
        $query = new ModelFromAttributesQuery();
        $query->set_model(static::class)
            ->add_to_meta_query($additionalMetaQueries)
            ->set_additional_queries($additionalQueries)
            ->set_post_statuses($postStatuses);

        foreach ($attributeValuePairs as $attribute => $value) {
            $query->add_attribute_value_to_meta_query($attribute, $value);
        }

        if ($flushCache) {
            $query->flush();
        }

        return $query->run();
    }

    /**
     * @param array $postStatuses
     * @param int $postsPerPage
     * @param int $offset
     * @param bool $flushCache
     * @return static[]
     */
    public static function all(array $postStatuses = [self::STATUS_PUBLISH], int $postsPerPage = -1, int $offset = 0, bool $flushCache = false): array
    {
        $query = new ModelAllQuery();
        $query->set_model(static::class)->set_post_statuses($postStatuses)->set_posts_per_page($postsPerPage)->set_offset($offset);

        if ($flushCache) {
            $query->flush();
        }

        return $query->run();
    }


    /**
     * @param int $postsPerPage
     * @param int $offset
     * @param bool $flushCache
     * @return static[]
     */
    public static function all_expired(int $postsPerPage = -1, int $offset = 0, bool $flushCache = false): array
    {
        return self::all([self::STATUS_EXPIRED], $postsPerPage, $offset, $flushCache);
    }

    /**
     * @param string $attribute
     * @param $value
     * @param array $postStatuses
     * @param int $postsPerPage
     * @param int $offset
     * @param bool $flushCache
     * @return static[]
     */
    public static function all_with_matching_attribute(string $attribute, $value, array $postStatuses = [self::STATUS_PUBLISH], int $postsPerPage = -1, int $offset = 0, bool $flushCache = false): array
    {
        $query = new ModelAllFromAttributeQuery();
        $query->set_model(static::class)
            ->add_attribute_value_to_meta_query($attribute, (string) $value)
            ->set_post_statuses($postStatuses)
            ->set_posts_per_page($postsPerPage)
            ->set_offset($offset);

        if ($flushCache) {
            $query->flush();
        }

        return $query->run();
    }

    /**
     * @param array $attributeValuePairs
     * @param array $additionalMetaQueries
     * @param array $additionalQueries
     * @param array $postStatuses
     * @param int $postsPerPage
     * @param int $offset
     * @param bool $flushCache
     * @return static[]
     */
    public static function all_with_matching_attributes(array $attributeValuePairs, array $additionalMetaQueries = [], array $additionalQueries = [], array $postStatuses = [self::STATUS_PUBLISH], int $postsPerPage = -1, int $offset = 0, bool $flushCache = false): array
    {
        $query = new ModelAllFromAttributesQuery();
        $query->set_model(static::class)
            ->add_to_meta_query($additionalMetaQueries)
            ->set_additional_queries($additionalQueries)
            ->set_post_statuses($postStatuses)
            ->set_posts_per_page($postsPerPage)
            ->set_offset($offset);

        foreach ($attributeValuePairs as $attribute => $value) {
            $query->add_attribute_value_to_meta_query($attribute, $value);
        }


        if ($flushCache) {
            $query->flush();
        }

        return $query->run();
    }

    /**
     * Sets all the attributes on the object, based on the post ID
     *
     * @param WP_Post $post
     * @return void
     */
    public function set_attributes(WP_Post $post)
    {
        $this->id = $post->ID;
        $this->post = $post;

        $metadata = get_post_meta($this->id);

        foreach(static::get_metadata_attribute_maps() as $metaKey => $attribute) {
            $this->$attribute = $metadata[$metaKey][0] ?? '';
        }
    }

    /**
     * Saves the post metadata
     *
     * @return void
     */
    public function save(): bool
    {
        if (!isset($this->id)) {
            return false;
        }

        foreach(static::get_metadata_attribute_maps() as $metaKey => $attribute) {
            if (!isset($this->$attribute)) {
                delete_post_meta($this->id, $metaKey);
            } else {
                update_post_meta($this->id, $metaKey, $this->$attribute);
            }
        }

        $postFieldsToUpdate = [
            'ID' => $this->id,
        ];

        foreach ($this->get_post_field_maps() as $postField => $modelField) {
            $postFieldsToUpdate[$postField] = $this->$modelField;
        }

        if (count($postFieldsToUpdate) > 1) {
            wp_update_post($postFieldsToUpdate);
        }

        return true;
    }

    /**
     * Updates a single attribute
     *
     * @param string $attribute
     * @return void
     */
    public function save_attribute(string $attribute)
    {
        foreach(static::get_metadata_attribute_maps() as $metaKey => $metadataAttributeMap) {
            if ($metadataAttributeMap !== $attribute) {
                continue;
            }

            if (!isset($this->$metadataAttributeMap)) {
                delete_post_meta($this->id, $metaKey);
            } else {
                update_post_meta($this->id, $metaKey, $this->$metadataAttributeMap);
            }

            break;
        }
    }

    /**
     * @param array $attributes
     * @return void
     */
    public function save_attributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->save_attribute($attribute);
        }
    }

    /**
     * Override this and provide an array of postfield => modelfield to have the post fields
     * automatically updated when the model is saved.
     *
     * @return array
     */
    public function get_post_field_maps(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function get_post_date(): string
    {
        return $this->post->post_date;
    }

	/**
	 * @return DateTime|null
	 */
	public function get_created_datetime(): ?DateTime
	{
		$postDate = $this->get_post_date();

		if (empty($postDate)) {
			return null;
		}

		$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $postDate);

		if (!$dateTime) {
			return null;
		}

		return $dateTime;
	}

    /**
     * @return string|null
     */
    public function get_post_status(): ?string
    {
        if (empty($this->post)) {
            return null;
        }

        return $this->post->post_status;
    }

    /**
     * @return string|null
     */
    public function get_post_type_description(): ?string
    {
        if (empty($this->post)) {
            return null;
        }

        $obj = get_post_type_object($this->post->post_type);

        if (!$obj) {
            return null;
        }

        return $obj->description;
    }

    /**
     * @return string|null
     */
    public function get_post_status_label(): ?string
    {
        $status = $this->get_post_status();

        switch($status) {
            case self::STATUS_PUBLISH:
                return 'Published';
            case self::STATUS_EXPIRED:
                return 'Expired';
            case self::STATUS_ARCHIVED:
                return 'Archived / Hidden';
            default:
                return $status;
        }
    }
}