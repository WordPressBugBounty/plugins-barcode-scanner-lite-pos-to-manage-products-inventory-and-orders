<?php

namespace UkrSolution\BarcodeScanner\API\classes;

use UkrSolution\BarcodeScanner\API\actions\ManagementActions;
use UkrSolution\BarcodeScanner\features\interfaceData\InterfaceData;

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
                OrdersHelper::setOrderFulfillmentDate($infoData, $orderId);

                update_post_meta($orderId, "usbs_order_fulfillment_data", $infoData);
            }
        } catch (\Throwable $th) {
        }
    }

    public static function addOrderData($orderId, &$data)
    {
        global $wpdb;

        if ($orderId) {
            $indexedOrderData = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}barcode_scanner_posts AS P WHERE post_id = %d", $orderId));

            if ($indexedOrderData) {
                $data["hook_order_number"] = $indexedOrderData->hook_order_number;
            }
        }
    }


    public static function recalculateOrderTotals($order) {
        foreach ($order->get_items('line_item') as $item_id => $item ) {
            $subtotal = $item->get_subtotal();
            $subtotal_tax = $item->get_subtotal_tax();



            $item->set_total($subtotal);
            $item->set_total_tax($subtotal_tax);

            $item->save();
        }

        $order->calculate_taxes();
        $order->calculate_totals();
        $order->save();
    }

    public static function assignOrderItemProps(&$products, &$order_subtotal_tax, &$order_subtotal_taxes, $order, $item, $settings) {
        global $wpdb;

        $variationId = $item->get_variation_id();
        $id = $variationId;

        if (!$id) {
            $id = $item->get_product_id();
        }
        $_post = get_post($id);


        if (!$_post) {
            $_post = (object)array("ID" => "", "post_parent" => "", "post_type" => "");
        }

        $product_thumbnail_url = get_the_post_thumbnail_url($_post->ID, 'medium');
        $product_large_thumbnail_url = get_the_post_thumbnail_url($_post->ID, 'large');

        if (!$product_thumbnail_url && $_post->post_parent) {
            $product_thumbnail_url = get_the_post_thumbnail_url($_post->post_parent, 'medium');
            $product_large_thumbnail_url = get_the_post_thumbnail_url($_post->post_parent, 'large');
        }


        $editId = $variationId && $_post->post_parent ? $_post->post_parent : $_post->ID;

        $args = array("currency" => " ", "thousand_separator" => "", "decimal_separator" => ".");

        $usbs_check_product_scanned = \wc_get_order_item_meta($item->get_id(), 'usbs_check_product_scanned', true);
        $usbs_check_product_scanned = $usbs_check_product_scanned == "" ? 0 : $usbs_check_product_scanned;

        $logRecord = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}barcode_scanner_logs AS L WHERE L.post_id = '{$item->get_id()}' AND L.field = 'usbs_check_product' AND L.action = 'update_order_item_meta' ORDER BY L.id DESC LIMIT 1");
        $fulfillment_user_name = "";
        $fulfillment_user_email = "";

        if ($logRecord && $logRecord->user_id) {
            $user = get_user_by("ID", $logRecord->user_id);

            if ($user) {
                $fulfillment_user_name = $user->display_name ? $user->display_name : $user->user_nicename;
                $fulfillment_user_email = $user->user_email;
            }
        }


        $quantity = \wc_get_order_item_meta($item->get_id(), '_qty', true);

        $product = $item->get_product();
        $variationForPreview = array();

        if ($product && $product->is_type('variation')) {
            $variation_attributes = $product->get_attributes();
            $variationForPreview = self::getOrderItemAttributeValues($variation_attributes, $item, $product);
        }

        if ($quantity) {
            $price_c = html_entity_decode(strip_tags(wc_price($item->get_total() / $quantity)), ENT_COMPAT | ENT_HTML5, 'UTF-8');
            $item_price_tax_c = html_entity_decode(strip_tags(wc_price(($item->get_subtotal() / $quantity) + $item->get_total_tax())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        } else {
            $price_c = html_entity_decode(strip_tags(wc_price($item->get_total())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
            $item_price_tax_c = html_entity_decode(strip_tags(wc_price($item->get_total_tax())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        }

        $item_regular_price_c = html_entity_decode(strip_tags(wc_price($product->get_price())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        $subtotal_c = html_entity_decode(strip_tags(wc_price($item->get_subtotal())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        $total_c = html_entity_decode(strip_tags(wc_price($item->get_total())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        $subtotal_tax_c = html_entity_decode(strip_tags(wc_price($item->get_subtotal_tax())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        $total_tax_c = html_entity_decode(strip_tags(wc_price($item->get_total_tax())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        $item_price_tax_total_c = html_entity_decode(strip_tags(wc_price($item->get_total() + $item->get_total_tax())), ENT_COMPAT | ENT_HTML5, 'UTF-8');

        $receiptShortcodes = ResultsHelper::getReceiptShortcodesOrderItem($settings, $order, $item);

        $_productData = array(
            "ID" => $_post->ID,
            "variation_id" => $variationId,
            "post_type" => $_post->post_type,
            "post_type_tooltip" => $_post->post_type == "product_variation" ? "variation" : "product", 
            "name" => strip_tags($item->get_name()),
            "quantity" => (float)$quantity,
            "price" => $quantity ? self::clearPrice($item->get_total() / $quantity, $args) : self::clearPrice($item->get_total(), $args),
            "price_c" => $price_c,
            "subtotal" => self::clearPrice($item->get_subtotal(), $args),
            "subtotal_c" => $subtotal_c,
            "total" => self::clearPrice($item->get_total(), $args),
            "total_c" => $total_c,
            "subtotal_tax" => self::clearPrice($item->get_subtotal_tax(), $args),
            "subtotal_tax_c" => $subtotal_tax_c,
            "total_tax" => self::clearPrice($item->get_total_tax(), $args),
            "total_tax_c" => $total_tax_c,
            "item_price_tax" => $quantity ? self::clearPrice(($item->get_subtotal() / $quantity) + $item->get_total_tax(), $args) : self::clearPrice($item->get_total_tax(), $args),
            "item_price_tax_c" => $item_price_tax_c,
            "item_price_tax_total" => self::clearPrice($item->get_subtotal() + $item->get_total_tax(), $args),
            "item_price_tax_total_c" => $item_price_tax_total_c,
            "item_regular_price" => self::clearPrice(get_post_meta($id, "_regular_price", true)), 
            "item_regular_price_c" => $item_regular_price_c, 
            "taxes" => strip_tags(wc_price($item->get_taxes())),
            "product_thumbnail_url" => $product_thumbnail_url,
            "product_large_thumbnail_url" => $product_large_thumbnail_url,
            "postEditUrl" => admin_url('post.php?post=' . $editId) . '&action=edit',
            "locations" => (new Results())->getLocations($_post->ID),
            "item_id" => $item->get_id(),
            "usbs_check_product" => \wc_get_order_item_meta($item->get_id(), 'usbs_check_product', true),
            "usbs_check_product_scanned" => $usbs_check_product_scanned,
            "fulfillment_user_name" => $fulfillment_user_name,
            "fulfillment_user_email" => $fulfillment_user_email,
            "product_categories" => wp_get_post_terms($item->get_product_id(), 'product_cat'),
            "variationForPreview" => $variationForPreview,
            "refund_data" => self::getOrderItemRefundData($order, $item),
            "receiptShortcodes" => $receiptShortcodes,
            "search_data" => InterfaceData::getIndexedData($_post->ID)
        );


        foreach (InterfaceData::getFields(true, "", false, Users::userRole()) as $value) {
            if (!$value['field_name']) continue;
            $filterName = str_replace("%field", $value['field_name'], "barcode_scanner_%field_get_after");
            $defaultValue = \get_post_meta($_productData["ID"], $value['field_name'], true);
            $filteredValue = apply_filters($filterName, $defaultValue, $value['field_name'], $_productData["ID"]);
            $filteredValue = $filteredValue;
            $_productData[$value['field_name']] = $filteredValue;
        }

        $filter = SearchFilter::get();

        if ($filter && isset($filter['products']) && is_array($filter['products'])) {
            foreach ($filter['products'] as $key => $value) {
                if (strpos($key, 'custom-') !== false) {
                    if (!isset($_productData[$value])) {
                        $defaultValue = \get_post_meta($_productData["ID"], $value, true);
                        $filteredValue = apply_filters($filterName, $defaultValue, $value, $_productData["ID"]);
                        $_productData[$value] = $filteredValue;
                    }
                }
            }
        }

        $number_field_step = get_post_meta($_productData["ID"], "number_field_step", true);

                if ($number_field_step && is_numeric($number_field_step)) {
            $_productData["number_field_step"] = (float)$number_field_step;
        } else {
            $_productData["number_field_step"] = 1;
        }


        $ffQtyStep = $settings->getSettings("ffQtyStep");
        $ffQtyStep = $ffQtyStep === null ? "" : $ffQtyStep->value;

        if ($ffQtyStep) {
            $_productData['ffQtyStep'] = get_post_meta($_productData["ID"], $ffQtyStep, true);
            if ($_productData['ffQtyStep']) $_productData['ffQtyStep'] = (float)$_productData['ffQtyStep'];
        }

        $products[] = $_productData;

        $_taxes = $item->get_taxes();

        if ($_taxes && isset($_taxes["total"]) && is_array($_taxes["total"])) {
            foreach ($_taxes["total"] as $tax_rate_id => $tax_amount) {
                if ($tax_amount) {
                    $order_subtotal_tax += $tax_amount;

                    if (isset($order_subtotal_taxes[$tax_rate_id])) {
                        $order_subtotal_taxes[$tax_rate_id]['cost'] += $tax_amount;
                        $order_subtotal_taxes[$tax_rate_id]['cost_c'] = ResultsHelper::getFormattedPrice(strip_tags(wc_price($order_subtotal_taxes[$tax_rate_id]['cost'])));
                    } else {
                        $order_subtotal_taxes[$tax_rate_id] = array(
                            'label' => \WC_Tax::get_rate_label($tax_rate_id),
                            'cost' => $tax_amount,
                            'cost_c' => ResultsHelper::getFormattedPrice(strip_tags(wc_price($tax_amount))),
                            'rate_id' => $tax_rate_id,
                        );
                    }
                }
            }
        }
    }

    private static function clearPrice($price, $args = array())
    {
        $wcPrice = wc_price($price, $args);
        $price = trim(strip_tags($wcPrice));
        $price = str_replace("&nbsp;", "", $price);
        $price = str_replace("&#36;", "", $price);

        return $price;
    }

    public static function getOrderItems($order_id, $types = array()) {
        global $wpdb;

        $items = array();

        if (!$types) {
            return $items;
        }

        $types = implode("','", $types);

        $order_items = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d AND order_item_type IN ('$types')", $order_id,)
        );

        foreach ($order_items as $item) {
            $item = new \WC_Order_Item_Product($item->order_item_id);
            $items[] = $item;
        }

        return $items;

            }

    public static function get_meta_value($order = null, $order_id = null, $meta_key = "", $default_value = null)
    {
        if (!$order && !$order_id) return $default_value;

        if (!$meta_key) return $default_value;

        $value = $default_value;

        if ($order) $value = $order->get_meta($meta_key, true);

        if (!$value && $order_id) $value = get_post_meta($order_id, $meta_key, true);

        return $value;
    }

    public static function setOrderFulfillmentDate(&$data, $orderId) {
        if (!$data || !is_array($data) || !isset($data["totalQty"]) || !$orderId) return $data;

        if ($data["totalQty"] && $data["totalScanned"] == $data["totalQty"]) {
            if (!$data["dateFulfilled"]) {
                $data["dateFulfilled"] = gmdate("Y-m-d H:i:s");
            }
        } 
        else {
            $data["dateFulfilled"] = "";
        }

        return $data;
    }

    public static function getOrderItemAttributeValues($attributes, $item, $product) {
        $variationForPreview = array();

        foreach ($attributes as $attribute_name => $attribute_value) {
            if (taxonomy_is_product_attribute($attribute_name)) {
                $attribute_label = wc_attribute_label($attribute_name);
            } 
            else {
                $attribute_label = wc_attribute_label($attribute_name, $product);
            }

            $item_attribute_value = \wc_get_order_item_meta($item->get_id(), $attribute_name, true);

            if ($item_attribute_value) {
                $attribute_value = $item_attribute_value;
            }

            $variationForPreview[] = array("label" => esc_html($attribute_label), "value" => esc_html($attribute_value));
        }

        return $variationForPreview;
    }
}
