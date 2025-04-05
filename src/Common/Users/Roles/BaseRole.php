<?php

namespace DigitalNature\WordPressUtilities\Common\Users\Roles;

abstract class BaseRole
{
    /**
     * @return string
     */
    public abstract static function get_role_slug(): string;

    /**
     * @return string
     */
    public abstract static function get_role_name(): string;

    /**
     * @return string[]
     */
    public abstract static function get_capabilities(): array;

    /**
     * @return void
     */
    public static function add_role(): void
    {
        $capabilities = array_fill_keys(static::get_capabilities(), true);

        add_role(
            static::get_role_slug(),
            static::get_role_name(),
            $capabilities
        );
    }
}