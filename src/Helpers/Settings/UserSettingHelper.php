<?php

namespace DigitalNature\WordPressUtilities\Helpers\Settings;

use Exception;
use WP_User;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class UserSettingHelper
{
    abstract public static function get_meta_key(): string;

    /**
     * @param WP_User $user
     * @return void
     */
    public static function enable(WP_User $user)
    {
        update_user_meta($user->ID, static::get_meta_key(), 1);
    }

    /**
     * Alias of SettingHelper::enable
     *
     * @param WP_User $user
     * @return void
     */
    public static function turn_on(WP_User $user)
    {
        self::enable($user);
    }

    /**
     * @param WP_User $user
     * @return void
     */
    public static function disable(WP_User $user)
    {
        if (!static::is_enabled($user)) {
            return;
        }

        delete_user_meta($user->ID, static::get_meta_key());
    }

    /**
     * Alias of SettingHelper::disable
     *
     * @param WP_User $user
     * @return void
     * @throws Exception
     */
    public static function turn_off(WP_User $user)
    {
        self::disable($user);
    }

    /**
     * Returns true if the setting is turned on for this user
     *
     * @param WP_User $user
     * @return bool
     */
    public static function is_enabled(WP_User $user): bool
    {
        return (bool) get_user_meta($user->ID, static::get_meta_key(), true);
    }

    /**
     * Alias of SettingHelper::disable
     *
     * @param WP_User $user
     * @return bool
     */
    public static function is_turned_on(WP_User $user): bool
    {
        return self::is_enabled($user);
    }

    /**
     * @param WP_User $user
     * @return bool
     */
    public static function is_set(WP_User $user): bool
    {
        return '' !== get_user_meta($user->ID, static::get_meta_key(), true);
    }

    /**
     * @param WP_User $user
     * @return void
     */
    public static function toggle(WP_User $user)
    {
        if (static::is_enabled($user)) {
            static::disable($user);
        } else {
            static::enable($user);
        }
    }
}