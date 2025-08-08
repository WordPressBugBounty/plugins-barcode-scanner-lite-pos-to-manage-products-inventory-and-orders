<?php

namespace UkrSolution\BarcodeScanner\API\actions;

use UkrSolution\BarcodeScanner\API\classes\OrdersHelper;
use UkrSolution\BarcodeScanner\API\classes\PickList;
use UkrSolution\BarcodeScanner\API\classes\Post;
use UkrSolution\BarcodeScanner\API\classes\ProductsHelper;
use UkrSolution\BarcodeScanner\API\classes\Results;
use UkrSolution\BarcodeScanner\API\classes\ResultsHelper;
use UkrSolution\BarcodeScanner\API\classes\SearchFilter;
use UkrSolution\BarcodeScanner\API\classes\Users;
use UkrSolution\BarcodeScanner\Database;
use UkrSolution\BarcodeScanner\features\cart\Cart;
use UkrSolution\BarcodeScanner\features\Debug\Debug;
use UkrSolution\BarcodeScanner\features\interfaceData\InterfaceData;
use UkrSolution\BarcodeScanner\features\settings\Settings;
use UkrSolution\BarcodeScanner\features\settings\SettingsHelper;

class HPOS
{
    private static $filter_search_query = "scanner_search_query";
    private static $filter_get_after = "barcode_scanner_%field_get_after";
    private static $limit = 20;
    private static $onlyById = false;
    private static $filterExcludes = array();

    public static function getStatus()
    {
        try {
            return \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() == true;
        } catch (\Throwable $th) {
            return false;
        }

        return false;
    }

    public static function findOrders($query, $filter, $onlyById, $autoFill, $filterExcludes)
    {
        global $wpdb;

        $settings = new Settings();

        $field = $settings->getSettings("searchResultsLimit");
        $searchResultsLimit = $field === null ? 20 : (int)$field->value;
        $searchResultsLimit = $searchResultsLimit ? $searchResultsLimit : 20;
        $searchResultsLimit = $searchResultsLimit > 999 ? 999 : $searchResultsLimit;

        $query = apply_filters(self::$filter_search_query, trim($query));

        if ($searchResultsLimit != self::$limit && $searchResultsLimit > 0) {
            self::$limit = $searchResultsLimit;
        }

        self::$onlyById = $onlyById;
        self::$filterExcludes = $filterExcludes;

        $excludeStatuses = "";
        $orderStatusesField = $settings->getSettings("orderStatuses");
        $excludeStatuses = $orderStatusesField === null ? "wc-checkout-draft,trash" : $orderStatusesField->value;

        $filterWithoutTitle = $filter;
        $findByTitle = false;

        if (self::isUrl($query)) {
            $query = urldecode($query);
        }

        $query = trim($query);
        $query = str_replace("'", "\'", $query);

        $queryFromUrl = self::checkUrlInQuery($query, $filter);

        if ($query != $queryFromUrl && is_numeric($queryFromUrl)) {
            if (isset($filterWithoutTitle["products"]) && $filterWithoutTitle["products"]["prod_link"]) {
                $filterWithoutTitle["products"]["ID"] = 1;
            }

            $query = $queryFromUrl;
        }

        $sql = self::sqlBuilder($query, $filterWithoutTitle, $excludeStatuses);

        $orders = $wpdb->get_results($sql);
        $total = $wpdb->get_row("SELECT FOUND_ROWS() as `total`");

        Debug::addPoint("1. sql");
        Debug::addPoint($sql);
        Debug::addPoint("1. orders = " . count($orders));

        if (count($orders)) {

            return array(
                "total" => $total ? $total->total : 0,
                "limit" => $searchResultsLimit,
                "posts" => $orders,
                "filter" => (new Post())->getFilterParams($filterWithoutTitle),
                "findByTitle" => $findByTitle,
                "query" => $query
            );
        }

        return array(
            "total" => $total ? $total->total : 0,
            "limit" => $searchResultsLimit,
            "posts" => null,
            "findByTitle" => null,
            "query" => $query
        );
    }

    private static function isUrl($query)
    {
        try {
            return preg_match("/.*?post=([\d]+).*?/", $query, $m) || (function_exists("wp_http_validate_url") && \wp_http_validate_url($query)) ? true : false;
        } catch (\Throwable $th) {
            return false;
        }
    }

    private static function checkUrlInQuery($query, $filter)
    {
        try {
            if (!$filter || !isset($filter["products"])) return $query;

            if (!isset($filter["products"]["prod_link"]) || $filter["products"]["prod_link"] != "1") return $query;

            $id = self::isUrl($query) ? url_to_postid($query) : "";

            if ($id) {
                return $id;
            } else if (preg_match("/.*?post=([\d]+).*?/", $query, $m)) {
                if (count($m) == 2 && is_numeric($m[1])) {
                    return $m[1];
                } else {
                    return $query;
                }
            } else {
                return $query;
            }
        } catch (\Throwable $th) {
            return $query;
        }
    }

    private static function sqlBuilder($query, $filter, $excludeStatuses)
    {
        global $wpdb;

        $queryLike = str_replace(" ", "%", $query);

        $posts = $wpdb->prefix . Database::$posts;
        $columns = $wpdb->prefix . Database::$columns;
        $metaFieldPrefix = Database::$postMetaFieldPrefix;

        $tableColumns = $wpdb->get_results("SELECT * FROM {$columns} AS C;");

        $statuses = explode(",", $excludeStatuses);
        if (!count($statuses)) $statuses[] = "trash";
        $statuses = implode("','", $statuses);

        $selectColumns = array();
        $params = (new Post())->getFilterParams($filter);
        $isNumeric = (preg_match('/^[0-9]{0,20}$/', trim($query), $m)) ? true : false;

        $sql = "SELECT SQL_CALC_FOUND_ROWS O.* %SELECT_COLUMNS% FROM {$posts} AS P, {$wpdb->prefix}wc_orders AS O 
                WHERE O.id = P.post_id AND ( P.post_status NOT IN('{$statuses}') OR P.post_status IS NULL ) 
                    AND ( P.post_parent = 0 OR P.post_parent IS NULL OR P.post_parent_status IS NUll OR P.post_parent_status NOT IN('{$statuses}')  ) 
                    AND ( ";

        $filterSql = "";

        if ($params["postID"] && $isNumeric) {
            if ($params["post_types"]) {
                $types = "'" . implode("', '", $params["post_types"]) . "'";

                if ($params["postID"] == 2) {
                    $filterSql .= " (P.post_id LIKE '%{$query}%' AND P.post_type IN({$types})) ";
                } else {
                    $filterSql .= " (P.post_id = '{$query}' AND P.post_type IN({$types})) ";
                }
            } else {
                $filterSql .= " P.post_id = '{$query}' ";
            }
        }

        if ($params["orderCF"] && $params["statusOrderCF"] != 0) {
            $types = "'shop_order'";
            $field = trim($params["orderCF"]);

            foreach ($tableColumns as $value) {
                if ($value->name === $field) {
                    $field = $value->column;
                    $filterSql .= (strlen($filterSql)) ? " OR " : "";
                    $_sql = self::sqlComp($params["statusOrderCF"], $types, $query, "P.{$field}");

                    $filterSql .= $_sql;
                    $selectColumns[] = "{$_sql} AS '{$field}'";
                    break;
                }
            }

            if (key_exists($field, Database::$postsFields)) {
                $filterSql .= (strlen($filterSql)) ? " OR " : "";
                $_sql = Database::$postsFields[$field] === "like" || $params["statusProductCF"] == 2 ? " ( P.post_type IN( {$types} ) AND  P.`{$field}` LIKE '%{$query}%' ) " :  " ( P.post_type IN( {$types} ) AND  P.`{$field}` = '{$query}' ) ";

                $filterSql .= $_sql;
                $selectColumns[] = "{$_sql} AS '{$field}'";
            }
        }

        foreach ($params as $_key => $_fieldName) {
            if (preg_match("/^order-custom-\d+$/", $_key, $m)) {
                $_status = isset($params[$m[0] . "-status"]) ? $params[$m[0] . "-status"] : 1;

                foreach ($tableColumns as $value) {
                    if ($value->name === $_fieldName && $value->table == "postmeta") {
                        $field = $value->column;

                        $filterSql .= (strlen($filterSql)) ? " OR " : "";
                        $types = "'shop_order'";
                        $_sql = self::sqlComp($_status, $types, $query, "P.{$field}");
                        $filterSql .= $_sql;
                        $selectColumns[] = "{$_sql} AS '{$field}'";
                    }
                }
            } else if (preg_match("/^order-item-\d+$/", $_key, $m)) {
                foreach ($tableColumns as $value) {
                    if ($value->name === $_fieldName && $value->table == "order-item") {
                        $field = $value->column;

                        $filterSql .= (strlen($filterSql)) ? " OR " : "";
                        $types = "'shop_order'";
                        $_sql = self::sqlComp(2, $types, $query, "P.{$field}");
                        $filterSql .= $_sql;
                        $selectColumns[] = "{$_sql} AS '{$field}'";
                    }
                }
            }
        }

        if ($params["_order_number"] != 0) {
            $filterSql .= (strlen($filterSql)) ? " OR " : "";
            $types = "'shop_order'";
            $_sql = self::sqlComp($params["_order_number"], $types, $query, "P.{$metaFieldPrefix}_order_number");
            $filterSql .= $_sql;
            $selectColumns[] = "{$_sql} AS '_order_number'";
        }

        if ($params["_billing_address_index"] != 0) {
            $filterSql .= (strlen($filterSql)) ? " OR " : "";
            $types = "'shop_order'";
            $_sql = self::sqlComp($params["_billing_address_index"], $types, $query, "P.{$metaFieldPrefix}_billing_address_index");
            $filterSql .= $_sql;
            $selectColumns[] = "{$_sql} AS '_billing_address_index'";
        }

        if ($params["_shipping_address_index"] != 0) {
            $filterSql .= (strlen($filterSql)) ? " OR " : "";
            $types = "'shop_order'";
            $_sql = self::sqlComp($params["_shipping_address_index"], $types, $query, "P.{$metaFieldPrefix}_shipping_address_index");
            $filterSql .= $_sql;
            $selectColumns[] = "{$_sql} AS '_shipping_address_index'";
        }

        if ($params["ywot_tracking_code"] != 0) {
            $filterSql .= (strlen($filterSql)) ? " OR " : "";
            $types = "'shop_order'";
            $_sql = self::sqlComp($params["ywot_tracking_code"], $types, $query, "P.{$metaFieldPrefix}ywot_tracking_code");
            $filterSql .= $_sql;
            $selectColumns[] = "{$_sql} AS 'ywot_tracking_code'";
        }
        if ($params["_wc_shipment_tracking_items"] != 0) {
            $filterSql .= (strlen($filterSql)) ? " OR " : "";
            $types = "'shop_order'";
            $_sql = self::sqlComp($params["_wc_shipment_tracking_items"], $types, $query, "P.{$metaFieldPrefix}_wc_shipment_tracking_items");
            $filterSql .= $_sql;
            $selectColumns[] = "{$_sql} AS '_wc_shipment_tracking_items'";
        }
        if ($params["_aftership_tracking_items"] != 0) {
            $filterSql .= (strlen($filterSql)) ? " OR " : "";
            $types = "'shop_order'";
            $_sql = self::sqlComp($params["_aftership_tracking_items"], $types, $query, "P.{$metaFieldPrefix}_aftership_tracking_items");
            $filterSql .= $_sql;
            $selectColumns[] = "{$_sql} AS '_aftership_tracking_items'";
        }

        $limit = self::$limit;
        $sql .= ($filterSql) ? $filterSql : " 1 != 1 ";
        $sql .= " ) GROUP BY P.ID ORDER BY P.post_modified DESC, P.post_date DESC LIMIT {$limit} ";

        if ($selectColumns) {
            $sql = str_replace("%SELECT_COLUMNS%", ", " . implode(", ", $selectColumns), $sql);
        } else {
            $sql = str_replace("%SELECT_COLUMNS%", " ", $sql);
        }

        return $sql;
    }

    private static function sqlComp($status, $types, $query, $field)
    {
        $like = str_replace("\'", "%", $query);

        return $status == 1
            ? " ( P.post_type IN( {$types} ) AND {$field} = '{$query}' ) "
            : " ( P.post_type IN( {$types} ) AND {$field} LIKE '%{$like}%' ) ";
    }

    public static function ordersPrepare($posts, $additionalFields = array(), $autoFill = false, $page = '')
    {
        $orders = array();

        if (!$posts) return $orders;

        if (count($posts) > 1) {
            foreach ($posts as $post) {
                $order = self::formatOrder($post, $additionalFields, 'orders_list');

                if ($order) $orders[] = $order;
            }
        } elseif (count($posts)) {
            $post = $posts[0];
            $order = self::formatOrder($post, $additionalFields, $page);

            if ($order) $orders[] = $order;
        }

        return $orders;
    }

    public static function formatOrder($post, $additionalFields = array(), $page = '')
    {
        $order = null;

        Debug::addPoint(" - formatOrder");

        try {
            if (!isset($post->ID) && isset($post->id)) $post->ID = $post->id;
            $order = new \WC_Order($post->ID);
        } catch (\Throwable $th) {
        }

        Debug::addPoint(" - formatOrder WC_Order");

        if ($order && $order->get_id()) {
            $reflect = new \ReflectionClass($order);


            Debug::addPoint(" - formatOrder ReflectionClass");


            if ($reflect->getShortName() === "OrderRefund") {
                return null;
            }

            if ($page == 'orders_list') {
                return self::assignOrderListProps($post, $order, $additionalFields, $page);
            } else {
                return self::assignOrderProps($post, $order, $additionalFields, $page);
            }
        }

        return null;
    }

    private static function assignOrderProps($post, $order, $additionalFields = array(), $page = '')
    {
        global $wpdb;

        Debug::addPoint(" - formatOrder assignOrderProps");

        $products = array();
        $items = $order->get_items("line_item");
        $currencySymbol = get_woocommerce_currency_symbol(get_woocommerce_currency());

        Debug::addPoint(" - formatOrder currency");

        $order_subtotal_tax = 0;
        $order_subtotal_taxes = array();

        $shipping_class_names = \WC()->shipping->get_shipping_method_class_names();

        $settings = new Settings();

        $order_shipping = 0;
        $order_shipping_tax = 0;
        $order_shipping_name = "";
        $order_shipping_title = "";

        Debug::addPoint(" - formatOrder shipping class names");

        foreach ($order->get_items("shipping") as $value) {
            if ($value->get_total()) {
                $order_shipping += $value->get_total();
            }

            if ($value->get_total_tax()) {
                $order_shipping_tax += $value->get_total_tax();
            }

            $order_shipping_name = $value->get_name();
            $order_shipping_title = $value->get_method_title();
            $method_id = $value->get_method_id();
            $instance_id = $value->get_instance_id();

            try {
                if ($shipping_class_names && isset($shipping_class_names[$method_id])) {
                    $method_instance = new $shipping_class_names[$method_id]($instance_id);

                    $order_shipping_title = $method_instance->method_title;
                }
            } catch (\Throwable $th) {
            }
        }

        Debug::addPoint(" - formatOrder shipping");

        $order_payment = $order->get_payment_method();
        $order_payment_title = $order->get_payment_method_title();

        $additionalTaxes = array();

        foreach ($order->get_items("fee") as $value) {
            $additionalTaxes[] = array(
                "label" => $value->get_name(),
                "value" => $value->get_total(),
                "value_c" => strip_tags(wc_price($value->get_total())),
                "tax" => $value->get_total_tax(),
                "tax_c" => strip_tags(wc_price($value->get_total_tax())),
                "plugin" => "",
            );
        }

        Debug::addPoint(" - formatOrder payment & fee");

        foreach ($items as $item) {
            OrdersHelper::assignOrderItemProps($products, $order_subtotal_tax, $order_subtotal_taxes, $order, $item, $settings);
        }

        Debug::addPoint(" - formatOrder items");

        $customerId = $order->get_customer_id();
        $user = $order->get_user();
        $userData = null;

        if ($customerId && $user) {
            $userData = $user->data;
            $userData->phone = get_user_meta($user->ID, 'billing_phone', true);
            $userData->avatar = @get_avatar_url($customerId);
        }

        $wpFormat = get_option("date_format", "F j, Y") . " " . get_option("time_format", "g:i a");
        $orderDate = new \DateTime($order->get_date_created());
        $date_format = $order->get_date_created();
        $date_format = $date_format->format("Y-m-d H:i:s");

        $customerName = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
        $customerName = trim($customerName);
        $customerCountry = $order->get_billing_country();
        $previewDateFormat = $orderDate->format("F j, Y");
        $previewDateFormat = SettingsHelper::dateTranslate($previewDateFormat);

        if (!$customerName && $customerId) {
            $customerName = get_user_meta($customerId, 'first_name', true) . ' ' . get_user_meta($customerId, 'last_name', true);
        }

        Debug::addPoint(" - formatOrder user");

        $logRecord = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}barcode_scanner_logs AS L WHERE L.post_id = '{$order->get_id()}' AND L.action = 'update_order_fulfillment' AND L.value = '1' ORDER BY L.id DESC LIMIT 1");
        $fulfillment_user_name = "";
        $fulfillment_user_email = "";

        if ($logRecord && $logRecord->user_id) {
            $user = get_user_by("ID", $logRecord->user_id);

            if ($user) {
                $fulfillment_user_name = $user->display_name ? $user->display_name : $user->user_nicename;
                $fulfillment_user_email = $user->user_email;
            }
        }

        $fulfillmentField = $settings->getSettings("orderFulFillmentField");
        $fulfillmentField = $fulfillmentField === null ? "" : $fulfillmentField->value;

        $orderStatusesAreStillNotCompleted = $settings->getSettings("orderStatusesAreStillNotCompleted");
        $orderStatusesAreStillNotCompleted = $orderStatusesAreStillNotCompleted === null ? "wc-pending,wc-processing,wc-on-hold" : $orderStatusesAreStillNotCompleted->value;

        $sortOrderItemsByCategories = $settings->getSettings("sortOrderItemsByCategories");
        $sortOrderItemsByCategories = $sortOrderItemsByCategories === null ? "" : $sortOrderItemsByCategories->value;

        Debug::addPoint(" - formatOrder fulfillment");

        $bStates = WC()->countries->get_states($order->get_billing_country());
        $bState  = !empty($bStates[$order->get_billing_state()]) ? $bStates[$order->get_billing_state()] : '';

        $sStates = WC()->countries->get_states($order->get_shipping_country());
        $sState  = !empty($sStates[$order->get_shipping_state()]) ? $sStates[$order->get_shipping_state()] : '';

        $receiptShortcodes = ResultsHelper::getReceiptShortcodesOrder($settings, $order->get_id());

        $cart = new Cart();

        if ($customerId) {
            $cartScannerActions = new CartScannerActions();

            $taxAddress = $cartScannerActions->getTaxAddress(array("address" => array(
                "billing_country" => $order->get_billing_country(),
                "billing_state" => $order->get_billing_state(),
                "billing_city" => $order->get_billing_city(),
                "billing_postcode" => $order->get_billing_postcode(),
                "shipping_country" => $order->get_shipping_country(),
                "shipping_state" => $order->get_shipping_state(),
                "shipping_city" => $order->get_shipping_city(),
                "shipping_postcode" => $order->get_shipping_postcode(),
                "shipping_as_billing" => 0
            )));

            $shippingMethods = $cart->getShippingMethods($customerId, $taxAddress);
        } 
        else {
            $userId = get_current_user_id();
            $shippingMethods = $cart->getShippingMethods($userId, array());
        }

        $shipping_method = "";

        foreach ($order->get_items('shipping') as $shipping_item) {
            $method_id = $shipping_item->get_method_id();
            if (!$method_id && $method_id != 0) $method_id = "";

            $instance_id = $shipping_item->get_instance_id();
            if (!$instance_id) $instance_id = 0;

            $shipping_method = $method_id;
        }

        $payment_method = $order->get_payment_method();

        $props = array(
            "ID" => $order->get_id(),
            "post_type" => $post->type ? $post->type : "shop_order",
            "post_author" => "",
            "data" => array(
                "billing" => array(
                    'first_name' => $order->get_billing_first_name(),
                    'last_name' => $order->get_billing_last_name(),
                    'company' => $order->get_billing_company(),
                    'email' => $order->get_billing_email(),
                    'phone' => $order->get_billing_phone(),
                    'address_1' => $order->get_billing_address_1(),
                    'address_2' => $order->get_billing_address_2(),
                    'postcode' => $order->get_billing_postcode(),
                    'city' => $order->get_billing_city(),
                    'state' => $order->get_billing_state(),
                    'state_name' => $bState,
                    'country' => $order->get_billing_country(),
                    'country_name' => $order->get_billing_country() ? WC()->countries->countries[$order->get_billing_country()] : "",
                ),
                "shipping" => array(
                    'first_name' => $order->get_shipping_first_name(),
                    'last_name' => $order->get_shipping_last_name(),
                    'company' => $order->get_shipping_company(),
                    'phone' => method_exists($order, 'get_shipping_phone') ? $order->get_shipping_phone() : "",
                    'address_1' => $order->get_shipping_address_1(),
                    'address_2' => $order->get_shipping_address_2(),
                    'postcode' => $order->get_shipping_postcode(),
                    'city' => $order->get_shipping_city(),
                    'state' => $order->get_shipping_state(),
                    'state_name' => $sState,
                    'country' => $order->get_shipping_country(),
                    'country_name' => $order->get_shipping_country() ? WC()->countries->countries[$order->get_shipping_country()] : "",
                ),
                "customer_note" => $order->get_customer_note(),
                "total_tax" => $order->get_total_tax(),
                "status" => $order->get_status(),
                "status_name" => wc_get_order_status_name($order->get_status()),
            ),
            "order_date" => $order->get_date_created(),
            "date_format" => $date_format,
            "preview_date_format" => $previewDateFormat,
            "usbs_fulfillment_objects" => get_post_meta($order->get_id(), "usbs_fulfillment_objects", true),
            "usbs_order_fulfillment_data" => get_post_meta($order->get_id(), "usbs_order_fulfillment_data", true),
            "user" => $userData,
            "order_tax" => $order->get_total_tax(),
            "order_tax_c" => strip_tags(wc_price($order->get_total_tax())),
            "order_subtotal" => $order->get_subtotal(),
            "order_subtotal_c" => strip_tags(wc_price($order->get_subtotal())),
            "order_subtotal_taxes" => array_values($order_subtotal_taxes),
            "order_subtotal_tax" => $order_subtotal_tax,
            "order_subtotal_tax_c" => strip_tags(wc_price($order_subtotal_tax)),
            "order_shipping" => $order_shipping,
            "order_shipping_c" => ResultsHelper::getFormattedPrice(strip_tags(wc_price($order_shipping))),
            "order_shipping_tax" => $order_shipping_tax,
            "order_shipping_tax_c" => ResultsHelper::getFormattedPrice(strip_tags(wc_price($order_shipping_tax))),
            "order_shipping_name" => $order_shipping_name,
            "order_shipping_title" => $order_shipping_title,
            "order_payment" => $order_payment,
            "order_payment_title" => $order_payment_title,
            "additionalTaxes" => $additionalTaxes,
            "order_total" => $order->get_total(),
            "order_total_c" => ResultsHelper::getFormattedPrice(strip_tags(wc_price($order->get_total()))),
            "customer_id" => $customerId,
            "customer_name" => trim($customerName),
            "customer_country" => $customerCountry,
            "products" => $sortOrderItemsByCategories == "on" && !in_array($page, array('history', 'orders_list')) ? ProductsHelper::sortProductsByCategories($products) : $products,
            "currencySymbol" => $currencySymbol,
            "statuses" => wc_get_order_statuses(),
            "postEditUrl" => admin_url('post.php?post=' . $post->ID) . '&action=edit',
            "postPayUrl" => $order->get_checkout_payment_url(),
            "updated" => time(),
            "foundCounter" => OrdersHelper::get_meta_value($order, $post->ID, "usbs_found_counter"),
            "fulfillment_user_name" => $fulfillment_user_name,
            "fulfillment_user_email" => $fulfillment_user_email,
            "discount" => $order->get_discount_total() ?  strip_tags($order->get_discount_to_display()) : "",
            "coupons" => $order->get_coupon_codes(),
            "shop" => ResultsHelper::getStoreData(),
            "receiptShortcodes" => $receiptShortcodes,
            "pickListTemplate" => !in_array($page, array('history', 'orders_list')) ? PickList::getTemplate($post, $order, $settings) : '',
            "customer_orders_count" => 0,
            "user_pending_orders_count" => ResultsHelper::get_user_pending_orders_count($customerId, $post->ID, $orderStatusesAreStillNotCompleted),
            "refund_data" => OrdersHelper::getOrderRefundData($order),
            "shippingMethods" => $shippingMethods,
            "shipping_method" => $shipping_method,
            "payment_method" => $payment_method,
        );

        OrdersHelper::addOrderData($order->get_id(), $props);

        Debug::addPoint(" - formatOrder order props");

        if ($customerId) {
            $customerOrders = $wpdb->get_row($wpdb->prepare(
                "SELECT COUNT(O.id) AS 'count' FROM {$wpdb->prefix}wc_orders AS O WHERE O.customer_id = %d AND O.`type` = 'shop_order';",
                $customerId
            ));

            $props["customer_orders_count"] = $customerOrders ? $customerOrders->count : 0;
        }

        Debug::addPoint(" - formatOrder customer orders");

        if ($fulfillmentField) {
            $props[$fulfillmentField] = OrdersHelper::get_meta_value($order, $post->ID, $fulfillmentField);
            $props[$fulfillmentField . "-filled"] = OrdersHelper::get_meta_value($order, $post->ID, $fulfillmentField . "-filled");
        }

        $props["_order_number"] = OrdersHelper::get_meta_value($order, $post->ID, "_order_number");
        $props["_billing_address_index"] = str_replace("<br/>", ", ", $order->get_formatted_billing_address());
        $props["_shipping_address_index"] = str_replace("<br/>", ", ", $order->get_formatted_shipping_address());
        $props["ywot_tracking_code"] = OrdersHelper::get_meta_value($order, $post->ID, "ywot_tracking_code");

        $props["_wc_shipment_tracking_items_list"] = array();
        $wcShipmentTrackingItems = OrdersHelper::get_meta_value($order, $post->ID, "_wc_shipment_tracking_items");
        $_wc_shipment_tracking_items = "";
        if ($wcShipmentTrackingItems && is_array($wcShipmentTrackingItems)) {
            foreach ($wcShipmentTrackingItems as $value) {
                if (isset($value["tracking_number"])) {
                    $_wc_shipment_tracking_items .= " " . $value["tracking_number"];
                    $props["_wc_shipment_tracking_items_list"][] = $value;
                }
            }
        }
        $props["_wc_shipment_tracking_items"] = trim($_wc_shipment_tracking_items);

        $aftershipTrackingItems = OrdersHelper::get_meta_value($order, $post->ID, "_aftership_tracking_items");
        $_aftership_tracking_items = "";
        if ($aftershipTrackingItems && is_array($aftershipTrackingItems)) {
            foreach ($aftershipTrackingItems as $value) {
                if (isset($value["tracking_number"])) $_aftership_tracking_items .= " " . $value["tracking_number"];
            }
        }
        $props["_aftership_tracking_items"] = trim($_aftership_tracking_items);

        foreach ($additionalFields as $key => $value) {
            $props[$key] = $value;
        }

        Debug::addPoint(" - formatOrder custom fields");

        return $props;
    }

    private static function assignOrderListProps($post, $order, $additionalFields = array(), $page = '')
    {
        $customerId = $order->get_customer_id();
        $user = $order->get_user();

        if ($customerId && $user) {
            $user = $user->data;
        }

        $wpFormat = get_option("date_format", "F j, Y") . " " . get_option("time_format", "g:i a");
        $orderDate = new \DateTime($order->get_date_created());
        $date_format = $order->get_date_created();
        $date_format = $date_format->format("Y-m-d H:i:s");


        $customerName = $order->get_billing_first_name() . " " . $order->get_billing_last_name();
        $customerName = trim($customerName);
        $previewDateFormat = $orderDate->format("M j, Y");
        $previewDateFormat = SettingsHelper::dateTranslate($previewDateFormat);

        if (!$customerName && $customerId) {
            $customerName = get_user_meta($customerId, 'first_name', true) . ' ' . get_user_meta($customerId, 'last_name', true);
        }

        $user = $order->get_user();
        $userData = null;

        if ($customerId && $user) {
            $userData = $user->data;
            $userData->avatar = @get_avatar_url($customerId);
        }

        $products = array();

        foreach ($order->get_items("line_item") as $item) {
            $products[] = array('item_id' => $item->get_id());
        }

        $settings = new Settings();

        $fulfillmentField = $settings->getSettings("orderFulFillmentField");
        $fulfillmentField = $fulfillmentField === null ? "" : $fulfillmentField->value;

        $props = array(
            "ID" => $order->get_id(),
            "post_type" => $post->type ? $post->type : "shop_order",
            "post_author" => "",
            "data" => array(
                "status" => $order->get_status(),
                "status_name" => wc_get_order_status_name($order->get_status()),
            ),
            "order_date" => $order->get_date_created(),
            "date_format" => $date_format,
            "preview_date_format" => $previewDateFormat,
            "usbs_fulfillment_objects" => get_post_meta($order->get_id(), "usbs_fulfillment_objects", true),
            "usbs_order_fulfillment_data" => get_post_meta($order->get_id(), "usbs_order_fulfillment_data", true),
            "order_total" => $order->get_total(),
            "order_total_c" => strip_tags(wc_price($order->get_total())),
            "customer_name" => trim($customerName),
            "total_products" => count($order->get_items("line_item")),
            "updated" => time(),
            "customer_orders_count" => 0,
            "user" => $userData,
            "products" => $products,
        );

        OrdersHelper::addOrderData($order->get_id(), $props);

        if ($fulfillmentField) {
            $props[$fulfillmentField] = OrdersHelper::get_meta_value($order, $post->ID, $fulfillmentField);
            $props[$fulfillmentField . "-filled"] = OrdersHelper::get_meta_value($order, $post->ID, $fulfillmentField . "-filled");
        }

        $props["_order_number"] = OrdersHelper::get_meta_value($order, $post->ID, "_order_number");

        return $props;
    }

    private static function clearPrice($price, $args = array())
    {
        $price = trim(strip_tags(wc_price($price, $args)));
        $price = str_replace("&nbsp;", "", $price);

        return $price;
    }
}
