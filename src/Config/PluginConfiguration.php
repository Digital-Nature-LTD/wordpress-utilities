<?php

namespace DigitalNature\WordPressUtilities\Config;

use DigitalNature\WordPressUtilities\Patterns\Singleton;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

abstract class PluginConfiguration extends Singleton
{
	public abstract static function get_prefix(): string;
	public abstract static function get_plugin_name(): string;
	public abstract static function get_plugin_friendly_name(): string;

	/**
	 * @return string
	 */
	public static function get_plugin_file(): string
	{
		return constant(static::get_prefix() . '_PLUGIN_FILE');
	}

	/**
	 * @return string
	 */
	public static function get_plugin_base(): string
	{
		return constant(static::get_prefix() . '_PLUGIN_BASE');
	}

	/**
	 * @return string
	 */
	public static function get_plugin_dir(): string
	{
		return constant(static::get_prefix() . '_PLUGIN_DIR');
	}

	/**
	 * @return string
	 */
	public static function get_plugin_url(): string
	{
		return constant(static::get_prefix() . '_PLUGIN_URL');
	}

	/**
	 * @return string
	 */
	public static function get_plugin_version(): string
	{
		return constant(static::get_prefix() . '_VERSION');
	}

	/**
	 * @param string $file
	 * @param string $version
	 * @return void
	 */
	public static function configure(string $file, string $version): void
	{
		// Plugin Root File
		define(static::get_prefix() . '_PLUGIN_FILE',	$file);

		// Plugin base
		define(static::get_prefix() . '_PLUGIN_BASE',	plugin_basename(static::get_plugin_file()));

		// Plugin Folder Path
		define(static::get_prefix() . '_PLUGIN_DIR',	plugin_dir_path(static::get_plugin_file()));

		// Plugin Folder URL
		define(static::get_prefix() . '_PLUGIN_URL',	plugin_dir_url(static::get_plugin_file()));

		// Plugin name
		define(static::get_prefix() . '_NAME',			static::get_plugin_name());

		// Plugin visible name
		define(static::get_prefix() . '_FRIENDLY_NAME', static::get_plugin_friendly_name());

		// Plugin version
		define(static::get_prefix() . '_VERSION',		$version);
	}
}