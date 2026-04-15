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
        $id = "";

        if ($order) {
            $name = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
            $name = trim($name);


            if ($fromLog) {
                // @codingStandardsIgnoreStart
                $logRecord = $wpdb->get_row($wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}barcode_scanner_logs AS L WHERE L.post_id = %d AND L.action = 'update_order_fulfillment' ORDER BY L.id DESC LIMIT 1",
                    $order->get_id()
                ));
                // @codingStandardsIgnoreEnd

                if ($logRecord && $logRecord->user_id) {
                    $user = get_user_by("ID", $logRecord->user_id);

                    if ($user) {
                        $id = $user->ID;
                        return array("name" => $user->display_name ? $user->display_name : $user->user_nicename, "id" => $id);
                    }
                }
            }

            if (!$name && $order->get_customer_id()) {

                $user = get_user_by("ID", $order->get_customer_id());

                if ($user) {
                    $name = $user->display_name ? $user->display_name : $user->user_nicename;
                    $id = $user->ID;
                }
            }
        }

        return array("name" => $name, "id" => $id);
    }

    public static function getOrderRefundData($order)
    {
        if (!$order)
            return null;

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
        if (!$order || !$item)
            return null;

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
                OrdersHelper::set_meta_value(null, $orderId, "usbs_order_fulfillment_data", $infoData);
            }
        } catch (\Throwable $th) {
        }
    }

    public static function addOrderData($orderId, &$data)
    {
        global $wpdb;

        if ($orderId) {
            // @codingStandardsIgnoreStart
            $indexedOrderData = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}barcode_scanner_posts AS P WHERE post_id = %d", $orderId));
            // @codingStandardsIgnoreEnd

            if ($indexedOrderData) {
                $data["hook_order_number"] = $indexedOrderData->hook_order_number;
            }
        }
    }


    public static function recalculateOrderTotals($order)
    {
        foreach ($order->get_items('line_item') as $item_id => $item) {
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

    public static function assignOrderItemProps(&$products, &$order_subtotal_tax, &$order_subtotal_taxes, $order, $item, $settings)
    {
        global $wpdb;

        $variationId = $item->get_variation_id();
        $id = $variationId;

        if (!$id) {
            $id = $item->get_product_id();
        }
        $_post = get_post($id);


        if (!$_post) {
            $_post = (object) array("ID" => "", "post_parent" => "", "post_type" => "");
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

        // @codingStandardsIgnoreStart
        $logRecord = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}barcode_scanner_logs AS L WHERE L.post_id = %d AND L.field = 'usbs_check_product' AND L.action = 'update_order_item_meta' ORDER BY L.id DESC LIMIT 1",
            $item->get_id()
        ));
        // @codingStandardsIgnoreEnd
        $fulfillment_user_name = "";
        $fulfillment_user_email = "";
        $fulfillment_user_id = "";

        if ($logRecord && $logRecord->user_id) {
            $user = get_user_by("ID", $logRecord->user_id);

            if ($user) {
                $fulfillment_user_name = $user->display_name ? $user->display_name : $user->user_nicename;
                $fulfillment_user_email = $user->user_email;
                $fulfillment_user_id = $user->ID;
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

        $item_regular_price_c = $product ? html_entity_decode(strip_tags(wc_price($product->get_price())), ENT_COMPAT | ENT_HTML5, 'UTF-8') : "";
        $subtotal_c = html_entity_decode(strip_tags(wc_price($item->get_subtotal())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        $total_c = html_entity_decode(strip_tags(wc_price($item->get_total())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        $subtotal_tax_c = html_entity_decode(strip_tags(wc_price($item->get_subtotal_tax())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        $total_tax_c = html_entity_decode(strip_tags(wc_price($item->get_total_tax())), ENT_COMPAT | ENT_HTML5, 'UTF-8');
        $item_price_tax_total_c = html_entity_decode(strip_tags(wc_price($item->get_total() + $item->get_total_tax())), ENT_COMPAT | ENT_HTML5, 'UTF-8');

        $receiptShortcodes = ResultsHelper::getReceiptShortcodesOrderItem($settings, $order, $item);

        $one_item_tax = $quantity ? self::clearPrice(($item->get_subtotal_tax() / $quantity), $args) : self::clearPrice($item->get_total_tax(), $args);
        $one_item_tax_c = $one_item_tax ? html_entity_decode(strip_tags(wc_price($one_item_tax)), ENT_COMPAT | ENT_HTML5, 'UTF-8') : $one_item_tax;

        $_productData = array(
            "ID" => $_post->ID,
            "variation_id" => $variationId,
            "post_type" => $_post->post_type,
            "post_type_tooltip" => $_post->post_type == "product_variation" ? "variation" : "product", 
            "name" => strip_tags($item->get_name()),
            "item_name" => strip_tags($item->get_name()),
            "item_qty" => (float) $quantity,
            "item_price_qty" => $total_c,
            "item_price_qty_tax_total" => $item_price_tax_total_c,
            "quantity" => (float) $quantity,
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
            "taxes_data" => self::formatItemTaxesData($item->get_taxes()),
            "product_thumbnail_url" => $product_thumbnail_url,
            "product_large_thumbnail_url" => $product_large_thumbnail_url,
            "postEditUrl" => admin_url('post.php?post=' . $editId) . '&action=edit',
            "locations" => (new Results())->getLocations($_post->ID),
            "item_id" => $item->get_id(),
            "usbs_check_product" => \wc_get_order_item_meta($item->get_id(), 'usbs_check_product', true),
            "usbs_check_product_scanned" => $usbs_check_product_scanned,
            "fulfillment_user_name" => $fulfillment_user_name,
            "fulfillment_user_email" => $fulfillment_user_email,
            "fulfillment_user_id" => $fulfillment_user_id,
            "product_categories" => wp_get_post_terms($item->get_product_id(), 'product_cat'),
            "variationForPreview" => $variationForPreview,
            "refund_data" => self::getOrderItemRefundData($order, $item),
            "receiptShortcodes" => $receiptShortcodes,
            "search_data" => InterfaceData::getIndexedData($_post->ID),
            "one_item_tax" => $one_item_tax,
            "one_item_tax_c" => $one_item_tax_c,
            "current_edit_data" => $order->get_meta("_bs_user_edit_data"),
            "pos_data" => self::getPosData($order),
        );


        foreach (InterfaceData::getFields(true, "", false, Users::userRole()) as $value) {
            if (!$value['field_name'])
                continue;
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
            $_productData["number_field_step"] = (float) $number_field_step;
        } else {
            $_productData["number_field_step"] = 1;
        }


        $ffQtyStep = $settings->getSettings("ffQtyStep");
        $ffQtyStep = $ffQtyStep === null ? "" : $ffQtyStep->value;

        if ($ffQtyStep) {
            $_productData['ffQtyStep'] = get_post_meta($_productData["ID"], $ffQtyStep, true);
            if ($_productData['ffQtyStep'])
                $_productData['ffQtyStep'] = (float) $_productData['ffQtyStep'];
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

    public static function formatItemTaxesData($taxes)
    {
        if (isset(($taxes['subtotal']))) {
            foreach ($taxes['subtotal'] as &$value) {
                $value = strip_tags(wc_price($value));
            }
        }

        if (isset(($taxes['total']))) {
            foreach ($taxes['total'] as &$value) {
                $value = strip_tags(wc_price($value));
            }
        }

        return $taxes;
    }
    private static function clearPrice($price, $args = array())
    {
        $wcPrice = wc_price($price, $args);
        $price = trim(strip_tags($wcPrice));
        $price = str_replace("&nbsp;", "", $price);
        $price = str_replace("&#36;", "", $price);

        return $price;
    }

    public static function getOrderItems($order_id, $types = array())
    {
        global $wpdb;

        $items = array();

        if (!$types) {
            return $items;
        }

        $types = implode("','", $types);

        // @codingStandardsIgnoreStart
        $order_items = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d AND order_item_type IN ('$types')", $order_id, )
        );
        // @codingStandardsIgnoreEnd

        foreach ($order_items as $item) {
            $item = new \WC_Order_Item_Product($item->order_item_id);
            $items[] = $item;
        }

        return $items;

    }

    public static function get_meta_value($order = null, $order_id = null, $meta_key = "", $default_value = null)
    {
        if (!$order && !$order_id) {
            return $default_value;
        }

        if (!$meta_key) {
            return $default_value;
        }

        $value = $default_value;

        if ($order) {
            $value = $order->get_meta($meta_key, true);
        }

        if (!$value && $order_id) {
            $value = get_post_meta($order_id, $meta_key, true);
        }

        return $value;
    }

    public static function set_meta_value($order = null, $order_id = null, $meta_key = "", $value = null)
    {
        if (!$order && $order_id) {
            $order = wc_get_order($order_id);
        }

        if ($order) {
            $order->update_meta_data($meta_key, $value);
            $order->save();
        }

        if ($order_id) {
            update_post_meta($order_id, $meta_key, $value);
        }

        return $value;
    }

    public static function setOrderFulfillmentDate(&$data, $orderId)
    {
        if (!$data || !is_array($data) || !isset($data["totalQty"]) || !$orderId) {
            return $data;
        }

        $order = wc_get_order($orderId);
        $currentData = $order ? OrdersHelper::get_meta_value($order, $orderId, "usbs_order_fulfillment_data") : null;

        if ($data["totalQty"] && $data["totalScanned"] == $data["totalQty"]) {
            if (!$data["dateFulfilled"]) {
                $data["dateFulfilled"] = gmdate("Y-m-d H:i:s");
                if ($currentData && !$currentData["dateFulfilled"]) {
                    if ($order) {
                        $order->add_order_note('Order Fulfilled', false, Users::userId());
                        $order->save();
                    }
                }
            }
        }
        else {
            $data["dateFulfilled"] = "";
            if ($currentData && $currentData["dateFulfilled"] != "") {
                $order = wc_get_order($orderId);
                if ($order) {
                    $order->add_order_note('Order Fulfillment was canceled', false, Users::userId());
                }
            }
        }

        return $data;
    }

    public static function getOrderItemAttributeValues($attributes, $item, $product)
    {
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

    public static function getOrderItemMetaFields($item)
    {
        $metaFields = array();

        $metaFields = apply_filters('scanner_filter_order_item_meta_fields', $metaFields, $item);

        return $metaFields;
    }

    public static function updateOrderItemMetaFields(&$result, $itemId, $orderId, $metaFields)
    {
        $updatedFields = apply_filters("barcode_scanner_update_order_item_meta_fields", array(), $itemId, $orderId, $metaFields);

        if ($updatedFields && $result && is_array($result)) {
            $result = array_merge($result, $updatedFields);
        }
    }

    public static function getOrderNotes($order)
    {
        $order_notes = array();

        if (!$order || !is_object($order))
            return $order_notes;

        $order_notes = \wc_get_order_notes(array('order_id' => $order->get_id()));

        return $order_notes;
    }

    public static function getPosData($order)
    {
        $posData = array();

        if (!$order || !is_object($order))
            return $posData;

        $_usbs_pos_stripe_id = $order->get_meta("_usbs_pos_stripe_id");

        if (!$_usbs_pos_stripe_id) {
            $_usbs_pos_stripe_id = get_post_meta($order->get_id(), '_usbs_pos_stripe_id', true);
        }

        if ($_usbs_pos_stripe_id) {
            $posData[] = array("label" => "Stripe ID", "value" => $_usbs_pos_stripe_id);
        }

        return $posData;
    }

    public static function removeOrderNote(\WP_REST_Request $request)
    {
        $orderId = $request->get_param("orderId");
        $noteId = $request->get_param("noteId");
        $userId = Users::getUserId($request);

        if (!$orderId || !$noteId || !$userId)
            return \rest_ensure_response(array("success" => false, "message" => "Invalid parameters"));

        $result = \wc_delete_order_note($noteId);
        if (!$result)
            return \rest_ensure_response(array("success" => false, "message" => "Failed to remove note"));

        $order_notes = \wc_get_order_notes(array('order_id' => $orderId));


        return \rest_ensure_response(array("success" => true, "order_notes" => $order_notes));
    }

    public static function addOrderNote(\WP_REST_Request $request)
    {
        $orderId = $request->get_param("orderId");
        $content = $request->get_param("content");
        $type = $request->get_param("type");
        $userId = Users::getUserId($request);

        if (!$orderId || !$content || !$userId)
            return \rest_ensure_response(array("success" => false, "message" => "Invalid parameters"));

        $order = new \WC_Order($orderId);
        if (!$order)
            return \rest_ensure_response(array("success" => false, "message" => "Failed to get order"));

        $order->add_order_note($content, $type == "customer", true);
        $order->save();

        $order_notes = \wc_get_order_notes(array('order_id' => $orderId));

        return \rest_ensure_response(array("success" => true, "order_notes" => $order_notes));
    }

    public static function checkEnableFFforOrder(\WP_REST_Request $request)
    {
        $orderId = $request->get_param("orderId");
        $userId = Users::getUserId($request);

        if (!$orderId || !$userId) {
            return \rest_ensure_response(array("success" => false, "message" => "Invalid parameters"));
        }

        $order = new \WC_Order($orderId);

        if (!$order) {
            return \rest_ensure_response(array("success" => false, "message" => "Order not found"));
        }

        $userEditData = $order->get_meta("_bs_user_edit_data");

        if ($userEditData) {
            $timestampLastActivites = isset($userEditData["timestamp"]) ? $userEditData["timestamp"] : 0;
            $editorId = isset($userEditData["user_id"]) ? $userEditData["user_id"] : 0;
            if ($timestampLastActivites >= (time() - 30) && $editorId != $userId) {
                return \rest_ensure_response(array("success" => true, "free_for_editing" => 0, "user_data" => $userEditData));
            }

        }

        $user = get_user_by("ID", $userId);

        if ($user) {
            $userName = $user->display_name ? $user->display_name : $user->user_nicename;
        }

        $_bs_user_edit_data = array("user_id" => $userId, "user_name" => $userName, "timestamp" => time());
        $order->update_meta_data("_bs_user_edit_data", $_bs_user_edit_data);
        $order->save();

        return \rest_ensure_response(array("success" => true, "free_for_editing" => 1, "user_data" => $order->get_meta("_bs_user_edit_data")));
    }

    public static function disableFFforOrder(\WP_REST_Request $request)
    {
        $orderId = $request->get_param("orderId");
        $userId = Users::getUserId($request);

        if (!$orderId || !$userId) {
            return \rest_ensure_response(array("success" => false, "message" => "Invalid parameters"));
        }

        $order = new \WC_Order($orderId);

        if (!$order) {
            return \rest_ensure_response(array("success" => false, "message" => "Order not found"));
        }

        $userEditData = $order->get_meta("_bs_user_edit_data");

        if ($userEditData) {
            $editorId = isset($userEditData["user_id"]) ? $userEditData["user_id"] : 0;
            if ($editorId == $userId) {
                $_bs_user_edit_data = array("user_id" => 0, "user_name" => "", "timestamp" => 0);
                $order->update_meta_data("_bs_user_edit_data", $_bs_user_edit_data);
                $order->save();

                return \rest_ensure_response(array("success" => true, "free_for_editing" => 0));
            }

        }

        return \rest_ensure_response(array("success" => true, "free_for_editing" => 1, "user_data" => $order->get_meta("_bs_user_edit_data")));
    }

    public static function setEditingFlagToOrder(\WP_REST_Request $request)
    {
        $orderId = $request->get_param("orderId");
        $userId = Users::getUserId($request);

        if (!$orderId || !$userId) {
            return \rest_ensure_response(array("success" => false, "message" => "Invalid parameters"));
        }

        $order = new \WC_Order($orderId);

        if (!$order) {
            return \rest_ensure_response(array("success" => false, "message" => "Order not found"));
        }

        $userEditData = $order->get_meta("_bs_user_edit_data");

        if ($userEditData) {
            $timestampLastActivites = isset($userEditData["timestamp"]) ? $userEditData["timestamp"] : 0;
            $editorId = isset($userEditData["user_id"]) ? $userEditData["user_id"] : 0;
            if ($timestampLastActivites >= (time() - 30) && $editorId != $userId) {
                return \rest_ensure_response(array("success" => true, "free_for_editing" => 0, "user_data" => $userEditData));
            }
        }

        $user = get_user_by("ID", $userId);

        if ($user) {
            $userName = $user->display_name ? $user->display_name : $user->user_nicename;
        }

        $_bs_user_edit_data = array("user_id" => $userId, "user_name" => $userName, "timestamp" => time());
        $order->update_meta_data("_bs_user_edit_data", $_bs_user_edit_data);
        $order->save();

        return \rest_ensure_response(array("success" => true, "user_data" => $order->get_meta("_bs_user_edit_data")));
    }

    public static function getWCshippingMethods()
    {
        try {
            $shippings = array();
            $shipping_methods = \WC()->shipping()->load_shipping_methods();
            $excluded_list = ["flat_rate", "free_shipping", "local_pickup", "pickup_location"];

            foreach ($shipping_methods as $key => $value) {
                if (!in_array($key, $excluded_list)) {
                    $shippings[] = array("id" => $key, "title" => $value->method_title);
                }
            }

            return $shippings;
        } catch (\Throwable $th) {
            return array();
        }
    }

    public static function orderAddProduct(\WP_REST_Request $request)
    {
        if (self::$managementActions == null) {
            self::$managementActions = new ManagementActions();
        }

        $productId = $request->get_param("productId");
        $orderId = $request->get_param("orderId");

        try {
            $order = wc_get_order($orderId);

            if (!$order) {
                return \rest_ensure_response(array("success" => false, "message" => "Order not found"));
            }

            $product = wc_get_product($productId);

            if (!$product) {
                return \rest_ensure_response(array("success" => false, "message" => "Product not found"));
            }

            $found = false;

            foreach ($order->get_items() as $item_id => $item) {
                if ($item->get_product_id() == $productId || $item->get_variation_id() == $productId) {
                    $qty = $item->get_quantity();
                    $total = $item->get_total();

                    $unit_price = $qty > 0 ? ($total / $qty) : 0;
                    $new_qty = $qty + 1;
                    $new_total = $unit_price * $new_qty;

                    $item->set_quantity($new_qty);
                    $item->set_subtotal($new_total);
                    $item->set_total($new_total);

                    $order->save();

                    \wc_update_order_item_meta($item_id, "usbs_check_product", "");
                    \wc_update_order_item_meta($item_id, "usbs_check_product_scanned", "");

                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $unit_price = $product->get_price();

                if (!$unit_price) {
                    return \rest_ensure_response(array("success" => false, "message" => __("This product doesn't have a price, you can't sell it.", "us-barcode-scanner")));
                }

                if ($product->get_type() == "simple") {
                    $item_id = $order->add_product($product, 1);
                } else {
                    $item_id = $order->add_product($product, 1, [
                        'variation_id' => $productId,
                    ]);
                }


                $order->save();

                OrdersHelper::checkOrderFulfillment($orderId);
            }

            return self::$managementActions->orderReCalculate($request);
        } catch (\Throwable $th) {
            return \rest_ensure_response(array("success" => false, "message" => $th->getMessage()));
        }
    }

    public static function removeProduct(\WP_REST_Request $request)
    {
        if (self::$managementActions == null) {
            self::$managementActions = new ManagementActions();
        }

        $itemId = $request->get_param("itemId");
        $orderId = $request->get_param("orderId");

        try {
            $order = wc_get_order($orderId);

            if (!$order) {
                return \rest_ensure_response(array("success" => false, "message" => "Order not found"));
            }

            if ($order) {
                $order->remove_item($itemId);
                $order->save();

                OrdersHelper::checkOrderFulfillment($orderId);
            }

            return self::$managementActions->orderReCalculate($request);
        } catch (\Throwable $th) {
            return \rest_ensure_response(array("success" => false, "message" => $th->getMessage()));
        }
    }
}
