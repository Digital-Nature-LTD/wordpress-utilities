<?php

namespace DigitalNature\WordPressUtilities\Models;

use DateTime;
use DigitalNature\WordPressUtilities\Factories\ModelFactory;
use DigitalNature\WordPressUtilities\Traits\CacheableModelTrait;
use WP_User;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ModelNote extends Model
{
    use CacheableModelTrait;

    const POST_TYPE = 'dn_model_note';

    const METADATA_KEY_MODEL_ID = 'model_id';
    const METADATA_KEY_NOTE = 'note';
    const METADATA_KEY_NOTE_AUTHOR_ID = 'author_user_id';
    const METADATA_KEY_NOTE_ORIGINAL_USER_ID = 'original_user_id';
    const METADATA_KEY_NOTE_WRITTEN_ON = 'created_on';

    public string $model_id;
    public string $note;
    public $author_user_id;
    public $original_user_id;
    public $created_on;

    /**
     * @return string
     */
    public static function get_post_type(): string
    {
        return self::POST_TYPE;
    }

    /**
     * @return string[]
     */
    public static function get_metadata_attribute_maps(): array
    {
        return [
            self::METADATA_KEY_MODEL_ID => 'model_id',
            self::METADATA_KEY_NOTE => 'note',
            self::METADATA_KEY_NOTE_AUTHOR_ID => 'author_user_id',
            self::METADATA_KEY_NOTE_ORIGINAL_USER_ID => 'original_user_id',
            self::METADATA_KEY_NOTE_WRITTEN_ON => 'created_on',
        ];
    }

    /**
     * @return array
     */
    public function get_metadata(): array
    {
        return [
            'Model ID' => get_post_meta($this->id, self::METADATA_KEY_MODEL_ID, true),
            'Note' => get_post_meta($this->id, self::METADATA_KEY_NOTE, true),
            'Author User ID' => get_post_meta($this->id, self::METADATA_KEY_NOTE_AUTHOR_ID, true),
            'Original User ID' => get_post_meta($this->id, self::METADATA_KEY_NOTE_AUTHOR_ID, true),
            'Written on' => get_post_meta($this->id, self::METADATA_KEY_NOTE_WRITTEN_ON, true),
        ];
    }

    /**
     * @return Model|null
     */
    public function get_model(): ?Model
    {
        if (empty($this->model_id)) {
            return null;
        }

        return ModelFactory::from_id($this->model_id);
    }

    /**
     * @return DateTime|null
     */
    public function get_created_datetime(): ?DateTime
    {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($this->created_on);

        return $dateTime;
    }

    /**
     * @return string|null
     */
    public function get_note(): ?string
    {
        if (empty($this->note)) {
            return null;
        }

        return $this->note;
    }

    /**
     * @return WP_User|null
     */
    public function get_author(): ?WP_User
    {
        $user = get_user_by('ID', $this->author_user_id);

        if (!$user) {
            return null;
        }

        return $user;
    }

    /**
     * @return WP_User|null
     */
    public function get_original_user(): ?WP_User
    {
        if (empty($this->original_user_id)) {
            return null;
        }

        $user = get_user_by('ID', $this->original_user_id);

        if (!$user) {
            return null;
        }

        return $user;
    }

    /**
     * @return string
     */
    public function get_author_name(): string
    {
        $author = $this->get_author();

        if ($author) {
            return "$author->first_name $author->last_name";
        }

        return 'system';
    }
}