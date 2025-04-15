<?php

namespace DigitalNature\WordPressUtilities\Common\Users\Roles;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

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
		// add the role with no capabilities
        add_role(
            static::get_role_slug(),
            static::get_role_name(),
            []
        );

		// get the role and add capabilities to ensure we're always up to date.
	    // If we don't do this then the capabilities are only added when the role
	    // is created, any capabilities added later will not be included.
		$role = get_role(static::get_role_slug());
	    $adminRole = get_role('administrator');

	    $capabilities = array_fill_keys(static::get_capabilities(), true);

		foreach ($capabilities as $capability => $granted) {
			$role->add_cap($capability);
			$adminRole->add_cap($capability);
		}
    }
}