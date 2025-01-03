<?php

namespace UkrSolution\BarcodeScanner\API\classes;

use DateTime;
use UkrSolution\BarcodeScanner\API\actions\ManagementActions;
use UkrSolution\BarcodeScanner\API\PluginsHelper;
use WP_REST_Request;

class BatchNumbers
{
    static private $hook_update_batch_fields = 'usbs_batch_numbers_update_batch_fields';
    static private $hook_after_update_batch_fields = 'usbs_batch_numbers_after_update_batch_fields';
    static private $hook_before_delete_batch = 'usbs_batch_numbers_before_delete_batch';
    static private $hook_after_delete_batch = 'usbs_batch_numbers_after_delete_batch';
    static private $hook_before_create_batch = 'usbs_batch_numbers_before_create_batch';
    static private $hook_after_created_batch = 'usbs_batch_numbers_after_created_batch';

    static public function status()
    {
        return PluginsHelper::is_plugin_active('woocommerce-product-batch-numbers/woocommerce-product-batch-numbers.php');
    }

    static public function addProductProps(&$fields)
    {
        global $wpdb;

        if (!$fields || !isset($fields['ID'])) return;

        $batchNumbers = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}wpo_wcpbn_batch_numbers AS BN, {$wpdb->prefix}wpo_wcpbn_shared_products AS SP 
            WHERE BN.id = SP.batch_id AND SP.product_id = %d;",
            $fields['ID']
        ));

        if ($batchNumbers) {
            foreach ($batchNumbers as &$value) {
                $value->editUrl = admin_url('edit.php?post_type=product&page=wpo-batch-numbers&tab=edit&batch=' . $value->id);
            }
        }

        $fields['batchNumbers'] = $batchNumbers ? $batchNumbers : array();
    }

    static public function removeBatch(WP_REST_Request $request)
    {
        global $wpdb;

        $batchId = (int)$request->get_param("id");
        $postId = (int)$request->get_param("postId");

        if ($batchId && $postId) {
            apply_filters(self::$hook_before_delete_batch, $postId, $batchId);

            $wpdb->delete("{$wpdb->prefix}wpo_wcpbn_batch_numbers", array("id" => $batchId));

            $wpdb->delete("{$wpdb->prefix}wpo_wcpbn_shared_products", array("batch_id" => $batchId, "product_id" => $postId));

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

            $dt = new DateTime('now');
            $userId = Users::getUserId($request);

            $wpdb->insert("{$wpdb->prefix}wpo_wcpbn_batch_numbers", array(
                "date_created" => $dt->format('Y-m-d H:i:s'),
                "date_expiry" => null,
                "user_id" => $userId,
            ));
            $batchId = $wpdb->insert_id;

            if ($batchId) {
                $wpdb->insert("{$wpdb->prefix}wpo_wcpbn_shared_products", array(
                    "date_created" => $dt->format('Y-m-d H:i:s'),
                    "batch_id" => $batchId,
                    "product_id" => $postId,
                ));
            }

            apply_filters(self::$hook_after_created_batch, $postId, $batchId);
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

            if ($field === 'quantity') {
                $record = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpo_wcpbn_batch_numbers AS BN WHERE BN.id = %d;", $batchId));

                if ($record && $record->quantity_available && (float)$record->quantity_available == 0) {
                    $fields['quantity_available'] = $value;
                }
            }

            $fields = apply_filters(self::$hook_update_batch_fields, $fields, $batchId);

            $wpdb->update("{$wpdb->prefix}wpo_wcpbn_batch_numbers", $fields, array("id" => $batchId));

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
                    'date_expiry' => $value['date_expiry'],
                    'batch_number' => $value['batch_number'],
                    'supplier' => $value['supplier'],
                    'quantity' => $value['quantity'],
                    'quantity_available' => $value['quantity_available'],
                    'status' => $value['status'],
                );

                $record = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpo_wcpbn_batch_numbers AS BN WHERE BN.id = %d;", $value['id']));

                if (
                    $record && $record->quantity && (float)$record->quantity == 0
                    && $record->quantity_available && (float)$record->quantity_available == 0
                ) {
                    $fields['quantity_available'] = $value['quantity'];
                }

                $fields = apply_filters(self::$hook_update_batch_fields, $fields, $value['id']);

                $wpdb->update("{$wpdb->prefix}wpo_wcpbn_batch_numbers", $fields, array("id" => $value['id']));

                apply_filters(self::$hook_after_update_batch_fields, $fields, $value['id'], $postId);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
