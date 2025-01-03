<?php

namespace UkrSolution\BarcodeScanner\API\classes;

use DateTime;
use UkrSolution\BarcodeScanner\API\actions\ManagementActions;
use UkrSolution\BarcodeScanner\API\PluginsHelper;
use WP_REST_Request;

class BatchNumbersWebis
{
    static private $hook_update_batch_fields = 'usbs_batch_numbers_webis_update_batch_fields';
    static private $hook_after_update_batch_fields = 'usbs_batch_numbers_webis_after_update_batch_fields';
    static private $hook_before_delete_batch = 'usbs_batch_numbers_webis_before_delete_batch';
    static private $hook_after_delete_batch = 'usbs_batch_numbers_webis_after_delete_batch';
    static private $hook_before_create_batch = 'usbs_batch_numbers_webis_before_create_batch';
    static private $hook_after_created_batch = 'usbs_batch_numbers_webis_after_created_batch';

    static public function status()
    {
        return PluginsHelper::is_plugin_active('product-batch-expiry-tracking-for-woocommerce/product-batch-expiry-tracking-for-woocommerce.php');
    }

    static public function addProductProps(&$fields)
    {
        global $wpdb;

        if (!$fields || !isset($fields['ID'])) return;

        $batchNumbers = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}webis_pbet AS BN WHERE BN.post_id = %d;",
            $fields['ID']
        ));

        $fields['batchNumbersWebis'] = array();
        $fields['wpbet-product-tracking-mode'] = '';

        if ($batchNumbers) {
            $fields['batchNumbersWebis'] = $batchNumbers;

            if (isset($fields['post_parent']) && $fields['post_parent']) {
                $fields['batchNumbersWebisMode'] = get_post_meta($fields['post_parent'], 'wpbet-product-tracking-mode', true);
            } else {
                $fields['batchNumbersWebisMode'] = get_post_meta($fields['ID'], 'wpbet-product-tracking-mode', true);
            }
        }
    }

    static public function removeBatch(WP_REST_Request $request)
    {
        global $wpdb;

        $batchId = (int)$request->get_param("id");
        $postId = (int)$request->get_param("postId");

        if ($batchId && $postId) {
            apply_filters(self::$hook_before_delete_batch, $postId, $batchId);

            $wpdb->delete("{$wpdb->prefix}webis_pbet", array("id" => $batchId));

            apply_filters(self::$hook_after_delete_batch, $postId);
        }

        $managementActions = new ManagementActions();
        $productRequest = new WP_REST_Request("", "");
        $productRequest->set_param("query", $postId);

        return $managementActions->productSearch($productRequest, false, true);
    }

    static public function addNewBatch(WP_REST_Request $request)
    {
        global $wpdb;

        $postId = (int)$request->get_param("postId");

        if ($postId) {
            apply_filters(self::$hook_before_create_batch, $postId);

            $wpdb->insert("{$wpdb->prefix}webis_pbet", array(
                "post_id" => $postId,
                "quantity" => 0,
            ));

            apply_filters(self::$hook_after_created_batch, $postId, $wpdb->insert_id);
        }

        $managementActions = new ManagementActions();
        $productRequest = new WP_REST_Request("", "");
        $productRequest->set_param("query", $postId);

        return $managementActions->productSearch($productRequest, false, true);
    }

    static public function saveBatchField(WP_REST_Request $request)
    {
        global $wpdb;

        $data = $request->get_param("data");
        $postId = (int)$request->get_param("postId");
        $batchId = isset($data['batchId']) ? (int)$data['batchId'] : null;
        $field = isset($data['field']) ? $data['field'] : null;
        $value = isset($data['value']) ? $data['value'] : null;

        if ($batchId && $field) {
            $fields = array($field => $value);

            $fields = apply_filters(self::$hook_update_batch_fields, $fields, $batchId);

            $wpdb->update("{$wpdb->prefix}webis_pbet", $fields, array("id" => $batchId));

            apply_filters(self::$hook_after_update_batch_fields, $fields, $batchId, $postId);
        }

        $managementActions = new ManagementActions();
        $productRequest = new WP_REST_Request("", "");
        $productRequest->set_param("query", $postId);

        return $managementActions->productSearch($productRequest, false, true);
    }

    static public function updateBatches($batches, $postId)
    {
        global $wpdb;

        if (!$batches) return;

        try {
            foreach ($batches as $key => $value) {
                $fields = array(
                    'expiry_date' => $value['expiry_date'],
                    'quantity' => $value['quantity'],
                    'batch_num' => $value['batch_num'],
                    'batch_date' => $value['batch_date'],
                    'supplier' => $value['supplier'],
                );

                $fields = apply_filters(self::$hook_update_batch_fields, $fields, $value['id']);

                $wpdb->update("{$wpdb->prefix}webis_pbet", $fields, array("id" => $value['id']));

                apply_filters(self::$hook_after_update_batch_fields, $fields, $value['id'], $postId);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
