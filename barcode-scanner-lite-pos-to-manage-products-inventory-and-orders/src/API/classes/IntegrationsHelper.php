<?php

namespace UkrSolution\BarcodeScanner\API\classes;

use Atum\Inc\Helpers;

class IntegrationsHelper
{
    public static function getAtumInventoryManagementFieldValue($id)
    {
        $fields = array(
            "atum_supplier_sku" => '',
            "atum_barcode" => '',
            "atum_supplier_id" => ''
        );

        if (!is_plugin_active('atum-stock-manager-for-woocommerce/atum-stock-manager-for-woocommerce.php')) {
            return $fields;
        }

        try {
            $product = Helpers::get_atum_product($id);

            if ($product) {
                $fields['atum_barcode'] = $product->get_barcode();
                $fields['atum_supplier_sku'] = $product->get_supplier_sku();
                $fields['atum_supplier_id'] = $product->get_supplier_id();
            }
        } catch (\Throwable $th) {
        }

        return $fields;
    }

    public static function removeEmoji($string)
    {
        $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clear_string = preg_replace($regex_emoticons, '', $string);

        $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clear_string = preg_replace($regex_symbols, '', $clear_string);

        $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clear_string = preg_replace($regex_transport, '', $clear_string);

        $regex_misc = '/[\x{2600}-\x{26FF}]/u';
        $clear_string = preg_replace($regex_misc, '', $clear_string);

        $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
        $clear_string = preg_replace($regex_dingbats, '', $clear_string);

        $clear_string = preg_replace('/[\x00-\x1F\x7F]/u', '', $clear_string);
        $clear_string = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $clear_string);
        $clear_string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $clear_string);

        try {
            $clear_string = filter_var($clear_string, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        } catch (\Throwable $th) {
        }

        return $clear_string;
    }
}
