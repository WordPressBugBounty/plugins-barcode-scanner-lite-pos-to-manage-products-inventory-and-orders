<?php

namespace UkrSolution\BarcodeScanner\API\classes;

use UkrSolution\BarcodeScanner\API\actions\ManagementActions;
use UkrSolution\BarcodeScanner\API\PluginsHelper;
use WP_REST_Request;

class ACFRepeater
{
    static public function status()
    {
        return PluginsHelper::is_plugin_active('advanced-custom-fields-pro/acf.php') && function_exists('get_field_objects') && function_exists('update_field');
    }

    public static function getProductData(&$fields)
    {
        global $wpdb;

        if (!$fields || !isset($fields['ID'])) {
            return;
        }
        $productId = $fields["ID"];
        $fields['acf_repeater'] = array();

        if ($productId && function_exists("get_field_objects")) {
            $fieldData = get_field_objects($productId);

            if ($fieldData) {
                $fields['acf_repeater'] = $fieldData;
            }
            else {
                $allGroups = \acf_get_field_groups();

                if ($allGroups) {
                    foreach ($allGroups as $group) {
                        $defaultFields = acf_get_fields($group['key']);

                        if ($defaultFields) {
                            foreach ($defaultFields as $value) {
                                $fields['acf_repeater'][$value['name']] = $value;
                            }
                        }
                    }
                }
            }
        }
    }

    public static function update(WP_REST_Request $request)
    {
        $postId = $request->get_param("postId");
        $data = $request->get_param("data");

        if (!$postId || !$data || !is_array($data)) {
            return \rest_ensure_response(array("success" => false));
        }

        foreach ($data as $field_name => $field_data) {
            $group_key = $field_data["key"];
            $values = $field_data["value"];

            update_field($group_key, $values, $postId);
        }

        $managementActions = new ManagementActions();
        $managementActions->productIndexation($postId, "ACFRepeater.update");

        $productRequest = new WP_REST_Request("", "");
        $productRequest->set_param("query", $postId);

        return $managementActions->productSearch($productRequest, false, true);
    }
}
