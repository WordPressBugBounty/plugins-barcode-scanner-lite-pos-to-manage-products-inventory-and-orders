<?php

namespace UkrSolution\BarcodeScanner\API\classes;

class RequestHelper
{
    public static function getRequestPriority()
    {
        $priority = 10;

        try {
            $post = json_decode(file_get_contents("php://input"), true);

            if ($post && isset($post['request_priority']) && $post['request_priority'] == "low") {                
                $priority = 999999;
            }
            else if ($post && isset($post['rout']) && in_array($post['rout'], array("changeStatus", "orderCreate", "updateProductMeta", "orderUpdateItemMeta"))) {
                $priority = 999999;
            }
            else if ($post && isset($post['rout']) && isset($post['fulfillmentOrderId']) && $post['fulfillmentOrderId']) {
                $priority = 999999;
            }
            else if ($post && isset($post['rout']) && in_array($post['rout'], array("getProductTaxonomy"))) {
                $priority = 999999;
            }
            else if ($post && isset($post['rout']) && in_array($post['rout'], array("userCreate"))) {
                $priority = 999999;
            }
            else if ($post && isset($post['rout']) && isset($post['orderAutoAction']) && !empty($post['orderAutoAction']) && $post['orderAutoAction'] != "empty") {
                $priority = 999999;
            }
            else if ($post && isset($post['rout']) && isset($post['postAutoAction']) && !empty($post['postAutoAction']) && $post['postAutoAction'] != "empty") {
                $priority = 999999;
            }
            else if ($post && isset($post['rout']) && in_array($post['rout'], array('update', 'batchNumbersRemoveBatch', 'batchNumbersAddNewBatch', 'batchNumbersSaveBatchField', 'batchNumbersWebisRemoveBatch', 'batchNumbersWebisAddNewBatch', 'batchNumbersWebisSaveBatchField'))) {
                $priority = 999999;
            }

            $priority = function_exists('barcode_scanner_request_priority') ? barcode_scanner_request_priority($priority, $post) : $priority;

        } catch (\Throwable $th) {
        }

        return $priority;
    }
}
