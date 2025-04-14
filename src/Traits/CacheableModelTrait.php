<?php

namespace DigitalNature\WordPressUtilities\Traits;

use DigitalNature\WordPressUtilities\Helpers\LogHelper;
use Exception;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

trait CacheableModelTrait
{
    /**
     * Gets the cache key name for the given prefix and arguments
     *
     * @param string $prefix
     * @param array $args Arguments to build the cache key, must be possible to cast each value to string
     * @return string
     */
    public static function cache_key_name(string $prefix, array $args = []): string
    {
        return $prefix . implode("_", $args);
    }

    /**
     * Attempts to retrieve one model from the cache
     *
     * @param string $cacheName
     * @param bool $flushCache
     * @return static|null
     */
    public static function cache_retrieve_one(string $cacheName, bool $flushCache) // : ?static // this is commented out as it's not supported on all PHP versions
    {
        try {
            if ($flushCache) {
                self::cache_delete($cacheName);
            } else {
                $cached = self::get_cached_model($cacheName);

                if ($cached) {
                    // the array will only contain one item, return it
                    return $cached;
                }
            }
        } catch (Exception $e) {
            LogHelper::write("Error retrieving single cache for $cacheName - {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Attempts to retrieve an array of models from the cache
     *
     * @param string $cacheName
     * @param bool $flushCache
     * @return static[]|null
     */
    public static function cache_retrieve_multiple(string $cacheName, bool $flushCache): ?array
    {
        try {
            if ($flushCache) {
                self::cache_delete($cacheName);
            } else {
                $cached = self::get_cached_models($cacheName);

                if ($cached) {
                    return $cached;
                }
            }
        } catch (Exception $e) {
            LogHelper::write("Error retrieving multi cache for $cacheName - {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Allows storing of arbitrary values against the model
     *
     * @param string $cacheName
     * @param bool $flushCache
     * @return mixed|null
     */
    public static function cache_retrieve_value(string $cacheName, bool $flushCache)
    {
        try {
            if ($flushCache) {
                self::cache_delete($cacheName);
            } else {
                $value = get_transient($cacheName);

                if (empty($value)) {
                    return null;
                }

                return $value;
            }
        } catch (Exception $e) {
			// fail silently
        }

        return null;
    }

    /**
     * Sets cache of models
     *
     * @param string $cacheName
     * @param static[] $records
     * @param int $expiry default to 1 hour
     * @return void
     */
    public static function cache_set(string $cacheName, array $records, int $expiry = 3600)
    {
        $cacheValue = array_column($records, 'id');

        set_transient($cacheName, $cacheValue, $expiry);
    }

    /**
     * Sets cache of arbitrary value
     *
     * @param string $cacheName
     * @param string $cacheValue
     * @param int $expiry default to 1 hour
     * @return void
     */
    public static function cache_set_value(string $cacheName, string $cacheValue, int $expiry = 3600)
    {
        set_transient($cacheName, $cacheValue, $expiry);
    }

    /**
     * Deletes cached values
     *
     * @param string ...$cacheNames
     * @return void
     */
    public static function cache_delete(string ...$cacheNames): void
    {
        foreach ($cacheNames as $cacheName) {
            delete_transient($cacheName);
        }
    }






    /**
     * Retrieves from cache, returning an array of records
     *
     * @param string $cacheName
     * @return static[]|null
     * @throws Exception
     */
    private static function get_cached_models(string $cacheName): ?array
    {
        $recordIds = get_transient($cacheName);

        if (empty($recordIds)) {
            LogHelper::write("CACHEABLE_MODEL_TRAIT: $cacheName Nothing found in cache");
            return null;
        }

        $models = [];

        foreach ($recordIds as $recordId) {
            $model = static::from_id($recordId);

            if ($model) {
                $models[] = $model;
            }
        }

        LogHelper::write("CACHEABLE_MODEL_TRAIT: $cacheName Retrieved " . count($models) . " models from cache");

        return $models;
    }

    /**
     * Retrieves from cache, returning a single model
     *
     * @param string $cacheName
     * @return static|null
     * @throws Exception
     */
    private static function get_cached_model(string $cacheName) // : ?static // this is commented out as it's not supported on all PHP versions
    {
        $cached = self::get_cached_models($cacheName);

        if (!$cached) {
            return null;
        }

        return end($cached);
    }
}