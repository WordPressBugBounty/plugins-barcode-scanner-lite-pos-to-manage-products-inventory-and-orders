<?php

namespace UkrSolution\BarcodeScanner\API;

class CouponHelper
{
    public static function createPercentCoupon($percent)
    {
        global $wpdb;

        $randomKey = substr(bin2hex(random_bytes(3)), 0, 6);
        $couponCode = "barcode_scanner_" . $percent . "%_";

        $_coupon_code = $wpdb->get_var($wpdb->prepare(
            "SELECT post_title FROM {$wpdb->posts} WHERE post_type = 'shop_coupon' AND post_status = 'publish' AND post_title LIKE %s LIMIT 1",
            $wpdb->esc_like($couponCode) . '%'
        ));

        if ($_coupon_code) {
            $couponData = new \WC_Coupon($_coupon_code);
        }

        if (!$couponData || !$couponData->get_id()) {
            $_coupon = new \WC_Coupon();
            $_coupon->set_code($couponCode . $randomKey);
            $_coupon->set_amount($percent);
            $_coupon->set_discount_type('percent');
            $_coupon->save();

            if ($_coupon->get_id()) {
                \update_post_meta($_coupon->get_id(), '_barcode_scanner_coupon', 'yes');
            }

            return new \WC_Coupon($couponCode . $randomKey);
        }

        return $couponData->get_id() ? $couponData : null;
    }

    public static function createFixedCoupon($fixed)
    {
        global $wpdb;

        $randomKey = substr(bin2hex(random_bytes(3)), 0, 6);
        $couponCode = "barcode_scanner_" . $fixed . "_";

        $_coupon_code = $wpdb->get_var($wpdb->prepare(
            "SELECT post_title FROM {$wpdb->posts} WHERE post_type = 'shop_coupon' AND post_status = 'publish' AND post_title LIKE %s LIMIT 1",
            $wpdb->esc_like($couponCode) . '%'
        ));

        if ($_coupon_code) {
            $couponData = new \WC_Coupon($_coupon_code);
        }

        if (!$couponData || !$couponData->get_id()) {
            $_coupon = new \WC_Coupon();
            $_coupon->set_code($couponCode . $randomKey);
            $_coupon->set_amount($fixed);
            $_coupon->set_discount_type('fixed_cart');
            $_coupon->save();

            if ($_coupon->get_id()) {
                \update_post_meta($_coupon->get_id(), '_barcode_scanner_coupon', 'yes');
            }

            return new \WC_Coupon($couponCode . $randomKey);
        }

        return $couponData->get_id() ? $couponData : null;
    }
}
