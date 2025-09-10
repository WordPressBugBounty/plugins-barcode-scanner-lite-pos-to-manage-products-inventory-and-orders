<?php

namespace UkrSolution\BarcodeScanner\API\actions;

use UkrSolution\BarcodeScanner\API\classes\OrdersHelper;
use UkrSolution\BarcodeScanner\API\classes\Post;
use UkrSolution\BarcodeScanner\API\classes\Results;
use UkrSolution\BarcodeScanner\API\classes\SearchFilter;
use UkrSolution\BarcodeScanner\API\RequestHelper;
use UkrSolution\BarcodeScanner\features\debug\Debug;
use WP_REST_Request;

class OrdersActions
{
    public $filter_fulfillment_step = 'scanner_fulfillment_step';

    public function ff2Search(WP_REST_Request $request)
    {
        Debug::addPoint("OrdersActions->ff2Search");

        $orderId = $request->get_param("orderId");
        $query = RequestHelper::getQuery($request, "product");
        $filter = SearchFilter::get();
        $result = array("products" => array(), "order_item_pick_info" => array());

        Debug::addPoint("---- start Post()->find");

        $data = (new Post())->find($query, $filter, false, true, null, "product", array(), array(), array(), "orders");

        $postsCount = $data && isset($data["posts"]) ? count($data["posts"]) : 0;
        $total =  $data && isset($data["total"]) ? $data["total"] : 0;
        $limit =  $data && isset($data["limit"]) ? $data["limit"] : 0;

        $result["total"] = $total && $limit && $total >= $limit ? $total : 0;

        Debug::addPoint("---- start Results()->productsPrepare");

        $products = (new Results())->productsPrepare($data["posts"], array());


        if ($products) {
            $order = new \WC_Order($orderId);

            if ($order) {
                foreach ($products as $product) {
                    $orderItem = $this->findProductInOrder($order, $product);

                    if ($orderItem) {
                        $id = $orderItem->get_variation_id();
                        $id = !$id ? $orderItem->get_product_id() : $id;
                        $quantity_scanned = \wc_get_order_item_meta($orderItem->get_id(), 'usbs_check_product_scanned', true);
                        $quantity = \wc_get_order_item_meta($orderItem->get_id(), '_qty', true);

                        $refund_data = OrdersHelper::getOrderItemRefundData($order, $orderItem);
                        $quantity += $refund_data["_qty"];

                        $result["order_item_pick_info"][] = array(
                            "item_id" => $orderItem->get_id(),
                            "qty" => $quantity,
                            "picked" => $quantity_scanned == "" ? 0 : $quantity_scanned
                        );

                        $product["item_id"] = $orderItem->get_id();
                    }

                    $result["products"][] = $product;
                }

                usort($result["products"], function ($a, $b) {
                    return $b["item_id"] - $a["item_id"];
                });
            }

            $managementActions = new ManagementActions();

            $customFilter["searchQuery"] = $query;

            $products = apply_filters($managementActions->filter_search_result, $products, $customFilter);

        }

        if (Debug::$status) {
            $result['debug'] = Debug::getResult();
        }

        return rest_ensure_response($result);
    }

    public function ff2PickItem(WP_REST_Request $request)
    {
        $orderId = $request->get_param("orderId");
        $itemId = $request->get_param("itemId");
        $qty = $request->get_param("quantity");

        if (!$orderId || !$itemId || !$qty) {
            return rest_ensure_response(array("success" => false));
        }

        $order = new \WC_Order($orderId);

        if (!$order) {
            return rest_ensure_response(array("success" => false));
        }

        $result = array("updatedOrder" => array());

        $managementActions = new ManagementActions();

        $items = OrdersHelper::getOrderItems($orderId, array('line_item', 'line_item_child'));

        foreach ($items as $value) {
            if ($itemId == $value->get_id()) {
                $pid = $value->get_variation_id() ? $value->get_variation_id() : $value->get_product_id();
                $productData  = array(
                    "ID" => $value->get_product_id(),
                    "variation_id" => $value->get_variation_id() ? $value->get_variation_id() : 0,
                    "number_field_step" => get_post_meta($pid, "number_field_step", true)
                );

                $filterOrderId = $orderId;
                $filterItemId = $itemId;
                $filterQty = $qty;

                add_filter("scanner_fulfillment_step", function ($step, $orderId, $productId, $itemId, $query) use ($filterOrderId, $filterItemId, $filterQty) {
                    if ($orderId == $filterOrderId && $itemId == $filterItemId) {
                        return $filterQty;
                    }

                    return $step;
                }, 1000, 5);

                $fulfillmentResult = $managementActions->applyFulfillment($request, $orderId, $productData, $itemId);

                if ($fulfillmentResult) {
                    if ($fulfillmentResult["error"]) {
                        return rest_ensure_response(array("success" => false, "error" => $fulfillmentResult["error"]));
                    }

                    if ($fulfillmentResult["updatedItems"]) {
                        $result["updatedItems"] = $fulfillmentResult["updatedItems"];
                    }
                }
            }
        }

        $result["updatedOrder"]["usbs_order_fulfillment_data"] = get_post_meta($orderId, "usbs_order_fulfillment_data", true);

        return rest_ensure_response($result);
    }

    public function ff2PickCustomField(WP_REST_Request $request)
    {
        $orderId = $request->get_param("orderId");
        $customField = $request->get_param("customField");

        if (!$orderId || !$customField) {
            return rest_ensure_response(array("success" => false));
        }

        $order = new \WC_Order($orderId);

        if (!$order) {
            return rest_ensure_response(array("success" => false));
        }

        $data = get_post_meta($orderId, "usbs_fulfillment_objects", true);
        $value = get_post_meta($orderId, $customField, true);
        $type = "tracking-code";

        if (!$data) $data = array();

        if ($value) {
            $data[$customField] = array("value" => $value, "type" => $type);
            update_post_meta($orderId, "usbs_fulfillment_objects", $data);

            $usbs_order_fulfillment_data = get_post_meta($orderId, "usbs_order_fulfillment_data", true);

            if ($usbs_order_fulfillment_data && isset($usbs_order_fulfillment_data['codes']) && is_array($usbs_order_fulfillment_data['codes'])) {
                $usbs_order_fulfillment_data_updated = false;

                foreach ($usbs_order_fulfillment_data['codes'] as $code_field => &$code_data) {
                    if ($code_field == $customField) {
                        $code_data['scanned'] = 1;
                        $usbs_order_fulfillment_data_updated = true;
                    }
                }

                if ($usbs_order_fulfillment_data_updated) {
                    OrdersHelper::setOrderFulfillmentDate($usbs_order_fulfillment_data, $orderId);

                    update_post_meta($orderId, "usbs_order_fulfillment_data", $usbs_order_fulfillment_data);
                }
            }
        }

        $orderRequest = new WP_REST_Request("", "");
        $orderRequest->set_param("query", $orderId);

        $managementActions = new ManagementActions();
        $managementActions->getFulfillmentOrderData($orderId);


        $result = array(
            "success" => true,
            "usbs_order_fulfillment_data" => get_post_meta($orderId, "usbs_order_fulfillment_data", true),
            "usbs_fulfillment_objects" => get_post_meta($orderId, "usbs_fulfillment_objects", true)
        );

        return rest_ensure_response($result);
    }

    public function ff2RepickItem(WP_REST_Request $request)
    {
        $orderId = $request->get_param("orderId");
        $itemId = $request->get_param("itemId");
    }

    private function findProductInOrder($order, $product)
    {
        $items = $order->get_items();

        foreach ($items as $item) {
            if ($item->get_product_id() == $product["ID"] && $item->get_product_id() === $product["post_parent"]) {
                if ($item->get_variation_id() == $product["variation_id"] || $item->get_product_id() == $product["variation_id"]) {
                    return $item;
                }
            } else if ($item->get_variation_id() == $product["ID"]) {
                if (isset($product["attributes"]) && $product["attributes"] && isset($product["requiredAttributes"])) {
                    foreach ($product["attributes"] as $attr => $value) {
                        if (isset($product["requiredAttributes"][$attr]) && $value == "") {
                            return false;
                        }
                    }
                }

                if ($item->attributes) {
                    $invalidValues = count($product["attributes"]);
                    $itemAttributes = @json_decode($item->attributes, false);
                    $itemAttributes = $itemAttributes ? (array)$itemAttributes : array();

                    foreach ($product["attributes"] as $attr => $value) {
                        if (
                            (isset($itemAttributes[$attr]) && $value == $itemAttributes[$attr])
                            || (isset($itemAttributes["attribute_{$attr}"]) && $value == $itemAttributes["attribute_{$attr}"])
                        ) {
                            $invalidValues--;
                        }
                    }

                    if ($invalidValues !== 0) {
                        continue;
                    }
                }

                return $item;
            } else if (isset($product["product_type"]) && $product["product_type"] == "simple") {
                if ($item->get_product_id() == $product["ID"]) {
                    return $item;
                }
            }
        }

        return false;
    }
}
