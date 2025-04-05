<?php

namespace DigitalNature\WordPressUtilities\Common\Users\Capabilities;

abstract class BaseCapability
{
	/**
	 * @return string
	 */
	public abstract static function get_capability_name(): string;
}