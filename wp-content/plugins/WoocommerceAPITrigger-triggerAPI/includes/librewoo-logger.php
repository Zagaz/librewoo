<?php 
// You shall not pass!

if (! defined('ABSPATH')) {
  exit;
}

/**
 * This class is responsible for logging messages to the WordPress database.
 * 
 */

class LibreSignLogger
{
    /**
     * Log a message to the WordPress database.
     * 
     * @param string $message
     * @param string $level
     * 
     * @return void
     */
    public static function librewoo_log($message, $level = 'info')
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'librewoo_logs';

        $wpdb->insert(
            $table_name,
            [
                'message' => $message,
                'level' => $level,
                'timestamp' => current_time('mysql')
            ]
        );
    }
}