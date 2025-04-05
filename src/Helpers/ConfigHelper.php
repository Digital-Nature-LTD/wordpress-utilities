<?php

namespace DigitalNature\WordPressUtilities\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class ConfigHelper
{
    /**
     * @return bool
     */
    public static function is_live_site(): bool
    {
        if (!defined('WP_ENVIRONMENT_TYPE')) {
            LogHelper::write('WP_ENVIRONMENT_TYPE is not defined, assuming NOT live site');
        }

        return defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'live';
    }

    /**
     * @return bool
     */
    public static function is_local_site(): bool
    {
        return !defined('WP_ENVIRONMENT_TYPE') || in_array(WP_ENVIRONMENT_TYPE, ['local', 'dev', 'development']);
    }

    /**
     * @return bool
     */
    public static function is_script(): bool
    {
        return defined('DN_IS_SCRIPT') && DN_IS_SCRIPT === true;
    }
}
