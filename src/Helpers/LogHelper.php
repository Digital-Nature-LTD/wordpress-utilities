<?php

namespace DigitalNature\WordPressUtilities\Helpers;

use DigitalNature\WordPressUtilities\Patterns\Singleton;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class LogHelper extends Singleton
{
    const LOCALDEV_FILENAME = 'local-debug.log';

    private array $logs = [];

    /**
     * @return bool
     */
    private static function write_to_file(): bool
    {
        return defined('DN_LOG_HELPER_WRITE_TO_FILE') && DN_LOG_HELPER_WRITE_TO_FILE === true;
    }

    /**
     * @param $log
     * @param bool $includeLineBreak
     * @return void
     */
    public static function write($log, bool $includeLineBreak = true)
    {
        $logHelper = self::getInstance();
        $logHelper->store_log($log);

        if (self::write_to_file()) {
            $logHelper::write_localdev($log);
        } elseif (ConfigHelper::is_script()) {
            echo $log . ($includeLineBreak ? '<br/>' : '');
        } elseif (ConfigHelper::is_local_site()) {
            $logHelper::write_localdev($log);
        } else {
            // live/staging etc. log to error_log
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

    /**
     * Logs a message in a custom file on the dev environment
     *
     * @param $log
     * @return void
     */
    private static function write_localdev($log): void
    {
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['basedir'];
        // If the entry is array, json_encode.
        if ( is_array( $log ) ) {
            $log = json_encode( $log );
        }
        // Write the log file.
        $file  = $upload_dir . '/' . self::LOCALDEV_FILENAME;
        $file  = fopen($file, 'a');
        $bytes = fwrite($file, current_time( 'mysql' ) . "::" . $log . "\n");
        fclose( $file );
    }

    /**
     * @param $log
     * @return void
     */
    private function store_log($log)
    {
        $this->logs[] = $log;

    }

    /**
     * @return array
     */
    public static function get_logs(): array
    {
        /** @var LogHelper $logHelper */
        $logHelper = self::getInstance();

        return $logHelper->logs;
    }

    /**
     * @return string|null
     */
    public static function get_last_log(): ?string
    {
        $logs = self::get_logs();

        if (empty($logs)) {
            return null;
        }

        return end($logs);
    }
}
