<?php

namespace DigitalNature\WordPressUtilities\Query\UserNote;

use DigitalNature\WordPressUtilities\Models\UserNote;
use DigitalNature\WordPressUtilities\Query\BaseModelQuery;
use WP_Post;
use WP_User;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class UserNoteQuery extends BaseModelQuery
{
    protected WP_User $user;

    /**
     * @return string
     */
    protected function get_cache_key(): string
    {
        $cacheParams = [];

        if (!empty($this->user)) {
            $cacheParams[] = $this->user->ID;
        }

        return UserNote::cache_key_name(get_called_class(), $cacheParams);
    }

	/**
	 * @param WP_User $user
	 *
	 * @return $this
	 */
    public function set_user(WP_User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return UserNote[]|null
     */
    protected function get_cached_result(): ?array
    {
        return UserNote::cache_retrieve_multiple($this->get_cache_key(), false);
    }

    /**
     * @param UserNote[] $models
     * @return void
     */
    protected function set_cached_result(array $models): void
    {
	    UserNote::cache_set($this->get_cache_key(), $models);
    }

    /**
     * @param WP_Post $post
     * @return UserNote|null
     */
    protected function hydrate_model(WP_Post $post): ?UserNote
    {
        return UserNote::from_post($post);
    }
}