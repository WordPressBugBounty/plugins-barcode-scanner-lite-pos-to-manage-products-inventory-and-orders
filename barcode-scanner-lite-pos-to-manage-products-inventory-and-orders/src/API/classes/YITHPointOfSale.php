<?php

namespace UkrSolution\BarcodeScanner\API\classes;

use UkrSolution\BarcodeScanner\API\PluginsHelper;

class YITHPointOfSale
{
    static public function status()
    {
        return PluginsHelper::is_plugin_active('yith-point-of-sale-for-woocommerce-premium/init.php');
    }

    static public function updateStore($postId, $storeId, $value)
    {
        $data = get_post_meta($postId, '_yith_pos_multistock', true);

        if (!$data) $data = array();

        foreach ($data as $id => &$qty) {
            if ($id == $storeId) $qty = $value;
        }

        if ($data) update_post_meta($postId, '_yith_pos_multistock', $data);
    }
}
