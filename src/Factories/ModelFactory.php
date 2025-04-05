<?php

namespace DigitalNature\WordPressUtilities\Factories;

use DigitalNature\WordPressUtilities\Helpers\CustomPostTypeHelper;
use DigitalNature\WordPressUtilities\Helpers\LogHelper;
use DigitalNature\WordPressUtilities\Models\Model;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ModelFactory
{
    /**
     * @param int $id
     * @return Model|null
     */
    public static function from_id(int $id): ?Model
    {
        $post = get_post($id);

        if (!$post) {
            return null;
        }

        $customPostTypeClass = CustomPostTypeHelper::get_post_type_class($post->post_type);

        if (!$customPostTypeClass) {
            return null;
        }

        if (!is_a($customPostTypeClass, Model::class, true)) {
            LogHelper::write("$customPostTypeClass is not an instance of Model");
            return null;
        }

        return $customPostTypeClass::from_id($id);
    }
}