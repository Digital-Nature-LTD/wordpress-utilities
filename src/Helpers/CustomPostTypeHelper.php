<?php

namespace DigitalNature\WordPressUtilities\Helpers;

use DigitalNature\WordPressUtilities\Models\Model;
use DigitalNature\WordPressUtilities\Stores\CustomPostTypeStore;
use Exception;
use WP_Error;
use WP_Post_Type;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class CustomPostTypeHelper
{
    /**
     * @param string $class
     * @param array $args
     * @return WP_Error|WP_Post_Type
     * @throws Exception
     */
    public static function register_post_type(string $class, array $args)
    {
        if (!is_a($class, Model::class, true)) {
            throw new Exception("$class is not an instance of Model");
        }

        self::set_post_type_class($class);

        return register_post_type( $class::get_post_type(), $args );
    }

    /**
     * @param string $class
     * @return void
     * @throws Exception
     */
    private static function set_post_type_class(string $class)
    {
        if (!is_a($class, Model::class, true)) {
            throw new Exception("$class is not an instance of Model");
        }

        CustomPostTypeStore::add_record($class, $class::get_post_type());
    }

    /**
     * @param string $postType
     * @return string|null
     */
    public static function get_post_type_class(string $postType): ?string
    {
        return CustomPostTypeStore::get_record($postType);
    }
}