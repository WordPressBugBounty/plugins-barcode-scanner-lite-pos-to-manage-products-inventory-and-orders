<?php

namespace UkrSolution\BarcodeScanner\API\classes;

class PostHelper
{
    public static function productSave($product_id, $fields = array())
    {
        try {
            $isOverride = apply_filters("scanner_override_product_save", false, $product_id, $fields);

            if ($isOverride == true) {
                return true;
            }

            return false;
        } catch (\Throwable $th) {
        }
    }

    public static function orderSave($order_id)
    {
        try {
            $isOverride = apply_filters("scanner_override_order_save", false, $order_id);

            if ($isOverride) {
                return;
            }

            $order->save();
        } catch (\Throwable $th) {
        }
    }
}
