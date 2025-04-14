<?php

namespace DigitalNature\WordPressUtilities\Config;

use DigitalNature\WordPressUtilities\Patterns\Singleton;

abstract class PluginConfiguration extends Singleton
{
	public abstract static function get_prefix(): string;

	/**
	 * @return string
	 */
	public static function get_plugin_name(): string
	{
		return constant(static::get_prefix() . '_PLUGIN_NAME');
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
	public static function get_plugin_dir(): string
	{

		return constant(static::get_prefix() . '_PLUGIN_DIR');
	}

	/**
	 * @return string
	 */
	public static function get_plugin_file(): string
	{
		return constant(static::get_prefix() . '_PLUGIN_FILE');
	}

	/**
	 * @param string $file
	 * @return void
	 */
	public static function configure(string $file): void
	{
		// Plugin Root File
		define(static::get_prefix() . '_PLUGIN_FILE',	$file);

		// Plugin base
		define(static::get_prefix() . '_PLUGIN_BASE',	plugin_basename(static::get_plugin_file()));

		// Plugin Folder Path
		define(static::get_prefix() . '_PLUGIN_DIR',	plugin_dir_path(static::get_plugin_file()));

		// Plugin Folder URL
		define(static::get_prefix() . '_PLUGIN_URL',	plugin_dir_url(static::get_plugin_file()));
	}
}