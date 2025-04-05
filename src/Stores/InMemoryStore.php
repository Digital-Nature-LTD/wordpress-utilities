<?php

namespace DigitalNature\WordPressUtilities\Stores;

use DigitalNature\WordPressUtilities\Patterns\Singleton;

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

abstract class InMemoryStore extends Singleton
{
    protected static array $instances = [];

    /**
     * @return string
     */
    private static function get_class_storage_index(): string
    {
        return static::class;
    }

    /**
     * @param $id
     * @param bool $purge
     * @return mixed|null
     */
    public static function get_record($id, bool $purge = false)
    {
        $index = self::get_class_storage_index();

        if (isset(self::$instances[$index][$id])) {
            if ($purge) {
                unset(self::$instances[$index][$id]);
            } else {
                // we already have it, return it
                return self::$instances[$index][$id];
            }
        }

        $record = static::retrieve_record($id);

        if (!$record) {
            return null;
        }

        // store this
        self::$instances[$index][$id] = $record;

        return $record;
    }

    /**
     * @return array
     */
    public static function get_all_records(): array
    {
        $index = self::get_class_storage_index();

        return self::$instances[$index] ?? [];
    }

    /**
     * Allows you to add an item into the store
     *
     * @param $record
     * @param null $id
     * @return void
     */
    public static function add_record($record, $id = null): void
    {
        $index = self::get_class_storage_index();

        if ($id) {
            self::$instances[$index][$id] = $record;
        } else {
            self::$instances[$index][] = $record;
        }
    }

    /**
     * @param $id
     * @return mixed|null
     */
    protected abstract static function retrieve_record($id);
}