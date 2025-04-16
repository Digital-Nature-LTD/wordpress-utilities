<?php

namespace DigitalNature\WordPressUtilities\Models;

use DateTime;
use DigitalNature\WordPressUtilities\Traits\CacheableModelTrait;
use WP_User;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class UserNote extends Model
{
    use CacheableModelTrait;

    const POST_TYPE = 'dn_user_audit_log';

    const METADATA_KEY_NOTE = 'note';
    const METADATA_KEY_NOTE_USER_ID = 'user_id';
    const METADATA_KEY_NOTE_WRITTEN_ON = 'created_on';

    public string $note;
    public $user_id;
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
	        self::METADATA_KEY_NOTE_USER_ID => 'user_id',
	        self::METADATA_KEY_NOTE => 'note',
            self::METADATA_KEY_NOTE_WRITTEN_ON => 'created_on',
        ];
    }

    /**
     * @return array
     */
    public function get_metadata(): array
    {
        return [
	        'User ID' => get_post_meta($this->id, self::METADATA_KEY_NOTE_USER_ID, true),
	        'Note' => get_post_meta($this->id, self::METADATA_KEY_NOTE, true),
            'Written on' => get_post_meta($this->id, self::METADATA_KEY_NOTE_WRITTEN_ON, true),
        ];
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
    public function get_user(): ?WP_User
    {
        $user = get_user_by('ID', $this->user_id);

        if (!$user) {
            return null;
        }

        return $user;
    }

	/**
	 * @return DateTime|null
	 */
	public function get_created_datetime(): ?DateTime
	{
		$dateTime = new DateTime();
		$dateTime->setTimestamp($this->created_on);

		return $dateTime;
	}
}