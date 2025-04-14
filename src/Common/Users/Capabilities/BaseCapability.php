<?php

namespace DigitalNature\WordPressUtilities\Common\Users\Capabilities;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class BaseCapability
{
	/**
	 * @return string
	 */
	public abstract static function get_capability_name(): string;
}