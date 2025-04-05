<?php

namespace DigitalNature\WordPressUtilities\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class MessageHelper
{
    /**
     * @param string $message
     * @return void
     */
    public static function error_and_exit(string $message)
    {
        $class = 'notice notice-error';
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );

        $logs = LogHelper::get_logs();

        echo "<h2>Log Messages</h2>";

        if (empty($logs)) {
            echo "<p>No logs</p>";
        } else {
            echo "<ul>";

            foreach($logs as $logMessage) {
                echo "<li>{$logMessage}</li>";
            }

            echo "</ul>";
        }

        exit;
    }
}
