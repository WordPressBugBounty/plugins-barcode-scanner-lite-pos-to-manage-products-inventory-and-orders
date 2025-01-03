<?php

namespace UkrSolution\BarcodeScanner\API\classes;

use UkrSolution\BarcodeScanner\API\actions\ManagementActions;

class OrdersHelper
{
    private static $managementActions = null;

    public static function getCustomerName($order, $fromLog = false)
    {
        global $wpdb;

        $name = "";

        if ($order) {
            $name = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
            $name = trim($name);


            if ($fromLog) {
                $logRecord = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}barcode_scanner_logs AS L WHERE L.post_id = '{$order->get_id()}' AND L.action = 'update_order_fulfillment' ORDER BY L.id DESC LIMIT 1");

                if ($logRecord && $logRecord->user_id) {
                    $user = get_user_by("ID", $logRecord->user_id);

                    if ($user) {
                        return $user->display_name ? $user->display_name : $user->user_nicename;
                    }
                }
            }

            if (!$name && $order->get_customer_id()) {

                $user = get_user_by("ID", $order->get_customer_id());

                if ($user) {
                    $name = $user->display_name ? $user->display_name : $user->user_nicename;
                }
            }
        }

        return $name;
    }

    public static function getOrderRefundData($order)
    {
        if (!$order) return null;

        $net_payment = $order->get_total() - $order->get_total_refunded();

        $refundData = array(
            "total_refunded" => $order->get_total_refunded(),
            "total_refunded_c" => ResultsHelper::getFormattedPrice(strip_tags(wc_price($order->get_total_refunded()))),
            "net_payment" => $net_payment,
            "net_payment_c" => ResultsHelper::getFormattedPrice(strip_tags(wc_price($net_payment))),
            "refunds" => array()
        );

        foreach ($order->get_refunds() as $refund) {
            $refundData["refunds"][] = array(
                "id" => $refund->get_id(),
                "reason" => $refund->get_reason(),
                "total" => $refund->get_amount(),
            );
        }

        return $refundData;
    }

    public static function getOrderItemRefundData($order, $item)
    {
        if (!$order || !$item) return null;

        $refundData = array("_qty" => 0);


        foreach ($order->get_refunds() as $refund) {
            foreach ($refund->get_items() as $refund_item) {
                if ($refund_item->get_meta('_refunded_item_id') == $item->get_id()) {

                    $refundData["_qty"] += $refund_item->get_quantity();
                }
            }
        }

        return $refundData;
    }

    public static function checkOrderFulfillment($orderId)
    {
        try {
            if (self::$managementActions == null) {
                self::$managementActions = new ManagementActions();
            }

            $infoData = self::$managementActions->getFulfillmentOrderData($orderId, false);

            if ($infoData) {
                update_post_meta($orderId, "usbs_order_fulfillment_data", $infoData);
            }
        } catch (\Throwable $th) {
        }
    }
}
