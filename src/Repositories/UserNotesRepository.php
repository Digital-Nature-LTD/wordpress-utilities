<?php

namespace DigitalNature\WordPressUtilities\Repositories;

use DigitalNature\WordPressUtilities\Helpers\LogHelper;
use DigitalNature\WordPressUtilities\Models\UserNote;
use DigitalNature\WordPressUtilities\Query\UserNote\UserNoteFromUserQuery;
use WP_User;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class UserNotesRepository
{
	/**
	 * @param WP_User $user
	 * @param string $content
	 * @param int|null $created_on
	 *
	 * @return UserNote
	 */
    public static function create(WP_User $user, string $content, int $created_on = null): UserNote
    {
        $note = UserNote::create();
        $note->user_id = $user->ID;
        $note->created_on = $created_on ?? time();
        $note->note = $content;
        $note->save();

        // allow other plugins to hook into this
        do_action('digital_nature_wordpress_utilities_user_note_created', $note);

        return $note;
    }

    /**
     * Deletes a model and clears relevant caches
     *
     * @param UserNote $note
     * @return void
     */
    public static function delete(UserNote $note): void
    {
        // delete this record
        $note->delete();

        // flush caches
        self::flush_caches($note);
    }



    /** MODEL CACHE FLUSH */

    /**
     * @param UserNote $note
     * @return void
     */
    public static function flush_caches(UserNote $note): void
    {
        LogHelper::write("DIGITAL_NATURE_MODEL_NOTE_REPOSITORY: Flushing caches");
        $user = $note->get_user();

        if ($user) {
            $query = new UserNoteFromUserQuery();
            $query->set_user($user)->flush();
        }
    }

	/**
	 * @param WP_User $user
	 * @param bool $flushCache
	 *
	 * @return UserNote[]
	 */
    public static function from_user(WP_User $user, bool $flushCache = false): array
    {
        $query = new UserNoteFromUserQuery();
        $query->set_user($user);

        if ($flushCache) {
            $query->flush();
        }

        return $query->run();
    }
}