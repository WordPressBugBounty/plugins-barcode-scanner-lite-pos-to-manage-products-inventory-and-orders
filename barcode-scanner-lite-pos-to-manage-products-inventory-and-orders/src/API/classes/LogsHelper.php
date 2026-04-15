<?php

namespace UkrSolution\BarcodeScanner\API\classes;

use UkrSolution\BarcodeScanner\Database;

class LogsHelper
{
    public static function add($message)
    {
        try {
            return self::saveToDb($message);

            if (!function_exists('wp_upload_dir')) {
                return;
            }

            $uploadDir = wp_upload_dir();
            $logDir = trailingslashit($uploadDir['basedir']) . 'barcode-scanner/log/';

            if (!file_exists($logDir)) {
                wp_mkdir_p($logDir);
            }

            $file = $logDir . 'log-' . date('Y-m-d') . '.log';

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $logLine = sprintf("[%s] %s%s", date('Y-m-d H:i:s'), $message, PHP_EOL);
            file_put_contents($file, $logLine, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $th) {
        }
    }

    private static function saveToDb($message)
    {
        global $wpdb;

        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        $message = sprintf("[%s] %s%s", date('Y-m-d H:i:s'), $message, PHP_EOL);
        $wpdb->insert($wpdb->prefix . Database::$systemLogs, array("user_id" => \get_current_user_id(), "message" => $message, "type" => "stripe"));
    }
}
