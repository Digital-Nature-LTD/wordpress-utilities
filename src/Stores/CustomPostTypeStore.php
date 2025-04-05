<?php

namespace DigitalNature\WordPressUtilities\Stores;

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

class CustomPostTypeStore extends InMemoryStore
{
    /**
     * Post types should be stored prior to use, they cannot be retrieved
     *
     * @param $id
     * @return null
     */
    protected static function retrieve_record($id)
    {
        return null;
    }
}