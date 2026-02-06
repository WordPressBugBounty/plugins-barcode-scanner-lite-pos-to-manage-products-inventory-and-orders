<?php

namespace UkrSolution\BarcodeScanner\API;

use WP_REST_Request;

class RequestHelper
{
    public static $scanner_search_query = 'barcode_scanner_search_query';

    public static function getQuery(WP_REST_Request $request, $type = "")
    {
        $query = $request->get_param("query");

        $query = apply_filters(self::$scanner_search_query, $query, $type);

        return $query ? trim($query) : "";
    }

    public static function initMemoryLimit()
    {
        $memoryLimit = ini_get('memory_limit');

        $memoryLimitBytes = self::convertToBytes($memoryLimit);

        if ($memoryLimitBytes < 512 * 1024 * 1024) {
            @ini_set('memory_limit', '512M');
        }

        return $memoryLimitBytes;
    }

    private static function convertToBytes($value)
    {
        if ($value === '-1') {
            return PHP_INT_MAX;
        }

        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $numeric = (int) $value;

        switch ($last) {
            case 'g':
                $numeric *= 1024;
            case 'm':
                $numeric *= 1024;
            case 'k':
                $numeric *= 1024;
        }

        return $numeric;
    }
}
