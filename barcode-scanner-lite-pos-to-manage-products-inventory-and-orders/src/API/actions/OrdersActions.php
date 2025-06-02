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

        $result = array("order_item_pick_info" => array());

        $managementActions = new ManagementActions();

        foreach ($order->get_items() as $orderItemId => $value) {
            if ($itemId == $orderItemId) {
                $pid = $value->get_variation_id() ? $value->get_variation_id() : $value->get_product_id();
                $productData  = array(
                    "ID" => $value->get_product_id(),
                    "variation_id" => $value->get_variation_id() ? $value->get_variation_id() : 0,
                    "number_field_step" => get_post_meta($pid, "number_field_step", true)
                );

                $fulfillmentResult = $managementActions->applyFulfillment($request, $orderId, $productData, $itemId);

                if ($fulfillmentResult) {
                    if ($fulfillmentResult["error"]) {
                        return rest_ensure_response(array("success" => false, "error" => $fulfillmentResult["error"]));
                    }

                    if ($fulfillmentResult["updatedItems"]) {
                        $result["updatedItems"] = $fulfillmentResult["updatedItems"];

                        foreach ($fulfillmentResult["updatedItems"] as $updatedItem) {
                            $quantity_scanned = \wc_get_order_item_meta($updatedItem["item_id"], 'usbs_check_product_scanned', true);
                            $quantity = \wc_get_order_item_meta($updatedItem["item_id"], '_qty', true);

                            $refund_data = OrdersHelper::getOrderItemRefundData($order, $value);
                            $quantity += $refund_data["_qty"];

                            $result["order_item_pick_info"][] = array(
                                "item_id" => $updatedItem["item_id"],
                                "qty" => $quantity,
                                "picked" => $quantity_scanned,
                            );
                        }
                    }
                }
            }
        }

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
