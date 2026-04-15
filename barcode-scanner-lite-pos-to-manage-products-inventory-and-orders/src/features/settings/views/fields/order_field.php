<?php

use UkrSolution\BarcodeScanner\API\classes\ACFRepeater;
use UkrSolution\BarcodeScanner\API\classes\BatchNumbers;
use UkrSolution\BarcodeScanner\API\classes\BatchNumbersWebis;
use UkrSolution\BarcodeScanner\API\classes\YITHPointOfSale;

?>
<tr class="settings_field_section field_<?php echo esc_attr($field["field_name"]); ?> <?php echo (isset($rootClass) && $rootClass) ? esc_attr($rootClass) : "" ?>">
    <td style="padding: 0; <?php if ($field[$statusField] == 0) {
        echo "opacity: 0.7;";
    } ?>">
        <div style="padding: 14px 10px 10px; background: #fff; margin-bottom: 10px; position: relative; width: 360px; box-shadow: 0 0 8px 1px #c7c7c7; border-radius: 4px;">
            <input type="hidden" class="usbs_field_order" name="fields[<?php echo esc_attr($field["id"]); ?>][<?php echo esc_attr($orderField); ?>]" value="<?php echo esc_attr($field[$orderField]); ?>" />
            <input type="hidden" class="usbs_field_position" name="fields[<?php echo esc_attr($field["id"]); ?>][position]" value="<?php echo esc_attr($field["position"]); ?>" />
            <input type="hidden" class="usbs_field_remove" name="fields[<?php echo esc_attr($field["id"]); ?>][remove]" value="0" />
            <input type="hidden" class="usbs_field_remove" name="fields[<?php echo esc_attr($field["id"]); ?>][label_position]" value="left" />

            <span class="dashicons dashicons-move" title="<?php echo esc_html__("Move", "us-barcode-scanner"); ?>"></span>

            <div class="settings_field_block_label" data-fid="<?php echo esc_attr($field["id"]); ?>">
                <span class="dashicons dashicons-arrow-up-alt2"></span>
                <span class="dashicons dashicons-arrow-down-alt2 active"></span>
                <?php 
                ?> <?php echo esc_html($field["field_label"]); ?>
                <?php if ($field[$statusField] == 0): ?>
                    <span style="color: #f00; position: relative; top: -4px;}"><?php echo esc_html__("(disabled)", "us-barcode-scanner"); ?></span>
                <?php endif; ?>
            </div>
            <!-- settings -->
            <div id="settings_field" class="settings_field_body" data-fid="<?php echo esc_attr($field["id"]); ?>">
                <div colspan="2" style="padding: 0;">
                    <table>
                        <tr class="usbs_field_status">
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <label onclick="WebbsFieldsChToggle(this, 'usbs_field_status')" data-fid="<?php echo esc_attr($field["id"]); ?>">
                                    <?php echo esc_html__("Enable", "us-barcode-scanner"); ?>
                                </label>
                            </td>
                            <td style="padding: 0 0 5px;">
                                <!-- checkbox -->
                                <?php
                                $_status = "status";
                                $checked = $field[$_status] == 1 ? ' ' . wp_kses_post('checked=checked') . ' ' : '';
                                ?>
                                <label>
                                    <input type="checkbox" class="usbs_field_status" <?php esc_html_e($checked, 'us-barcode-scanner'); ?> data-fid="<?php echo esc_attr($field["id"]); ?>"
                                        onchange="WebbsSettingsCheckboxChange(`#bs-settings-fields-tab .usbs_field_status input[data-fid='<?php echo esc_attr($field['id']); ?>']`, this.checked ? '1' : '0')" />
                                    <input type="hidden" name="fields[<?php echo esc_attr($field["id"]); ?>][<?php echo esc_attr($_status); ?>]" value="<?php echo $checked ? "1" : "0"; ?>" data-fid="<?php echo esc_attr($field["id"]); ?>" />
                                    <?php echo esc_html__("Desktop", "us-barcode-scanner"); ?>
                                </label>
                                <!-- checkbox -->
                                <?php
                                $_status = "mobile_status";
                                $checked = $field[$_status] == 1 ? ' ' . wp_kses_post('checked=checked') . ' ' : '';
                                ?>
                                <label>
                                    <input type="checkbox" class="usbs_field_mobile_status" <?php esc_html_e($checked, 'us-barcode-scanner'); ?> data-fid="<?php echo esc_attr($field["id"]); ?>"
                                        onchange="WebbsSettingsCheckboxChange(`#bs-settings-fields-tab .usbs_field_status input[data-fid-mobile='<?php echo esc_attr($field['id']); ?>']`, this.checked ? '1' : '0')" />
                                    <input type="hidden" name="fields[<?php echo esc_attr($field["id"]); ?>][<?php echo esc_attr($_status); ?>]" value="<?php echo $checked ? "1" : "0"; ?>" data-fid-mobile="<?php echo esc_attr($field["id"]); ?>" />
                                    <?php echo esc_html__("Mobile", "us-barcode-scanner"); ?>
                                </label>
                            </td>
                        </tr>

                        <tr class="read_only">
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <label onclick="WebbsFieldsChToggle(this, 'usbs_field_read_only')" data-fid="<?php echo esc_attr($field["id"]); ?>">
                                    <?php echo esc_html__("Read-only", "us-barcode-scanner"); ?>
                                </label>
                            </td>
                            <td style="padding: 0 0 5px;">
                                <!-- checkbox -->
                                <?php $checked = $field["read_only"] == 1 ? ' checked=checked ' : ''; ?>
                                <input type="checkbox" class="usbs_field_read_only" <?php esc_html_e($checked, 'us-barcode-scanner'); ?> data-fid="<?php echo esc_attr($field["id"]); ?>"
                                    onchange="WebbsSettingsCheckboxChange(`#bs-settings-fields-tab .read_only input[data-fid='<?php echo esc_attr($field['id']); ?>']`, this.checked ? '1' : '0')" />
                                <input type="hidden" name="fields[<?php echo esc_attr($field["id"]); ?>][read_only]" value="<?php echo $checked ? esc_attr("1") : esc_attr("0"); ?>" data-fid="<?php echo esc_attr($field["id"]); ?>" />
                            </td>
                        </tr>

                        <tr class="tr_usbs_field_type">
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <?php echo esc_html__("Field type", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 0;">
                                <select class="usbs_field_type" name="fields[<?php echo esc_attr($field["id"]); ?>][type]" style="width: 177px;">
                                    <option value="text" <?php echo $field["type"] == "text" ? wp_kses_post("selected='selected'") : ""; ?>><?php echo esc_html__("Text", "us-barcode-scanner"); ?></option>
                                    <option value="number_plus_minus" <?php echo $field["type"] == "number_plus_minus" ? wp_kses_post("selected='selected'") : ""; ?>><?php echo esc_html__("Number (plus/minus)", "us-barcode-scanner"); ?></option>
                                    <option value="select" <?php echo $field["type"] == "select" ? wp_kses_post("selected='selected'") : ""; ?>><?php echo esc_html__("Dropdown", "us-barcode-scanner"); ?></option>
                                    <option value="action_button" <?php echo $field["type"] == "action_button" ? wp_kses_post("selected='selected'") : ""; ?>><?php echo esc_html__("JS Button", "us-barcode-scanner"); ?></option>
                                    <option value="usbs_date" <?php echo $field["type"] == "usbs_date" ? wp_kses_post("selected='selected'") : ""; ?>><?php echo esc_html__("Date", "us-barcode-scanner"); ?></option>
                                    <option value="checkbox" <?php echo $field["type"] == "checkbox" ? wp_kses_post("selected='selected'") : ""; ?>><?php echo esc_html__("Checkbox", "us-barcode-scanner"); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr class="show_in_products_list" style="<?php echo !$isMobile ? "display: none;" : ""; ?>">
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <label onclick="WebbsFieldsChToggle(this, 'usbs_field_show_in_products_list')" data-fid="<?php echo esc_attr($field["id"]); ?>">
                                    <?php echo esc_html__("Show in mobile list", "us-barcode-scanner"); ?>
                                </label>
                            </td>
                            <td style="padding: 0 0 5px;">
                                <!-- checkbox -->
                                <?php $checked = $field["show_in_products_list"] == 1 ? ' checked=checked ' : ''; ?>
                                <input type="checkbox" class="usbs_field_show_in_products_list" <?php esc_html_e($checked, 'us-barcode-scanner'); ?> data-fid="<?php echo esc_attr($field["id"]); ?>"
                                    onchange="WebbsSettingsCheckboxChange(`#bs-settings-fields-tab .show_in_products_list input[data-fid='<?php echo esc_attr($field['id']); ?>']`, this.checked ? '1' : '0')" />
                                <input type="hidden" name="fields[<?php echo esc_attr($field["id"]); ?>][show_in_products_list]" value="<?php echo $checked ? esc_attr("1") : esc_attr("0"); ?>" data-fid="<?php echo esc_attr($field["id"]); ?>" />
                            </td>
                        </tr>

                        <tr class="show_on_mobile_preview" style="<?php echo !$isMobile ? "display: none;" : ""; ?>" data-is-mobile="<?php echo $isMobile ? "1" : "0"; ?>">
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <label onclick="WebbsFieldsChToggle(this, 'usbs_field_show_on_mobile_preview')" data-fid="<?php echo esc_attr($field["id"]); ?>">
                                    <?php echo esc_html__("Show on product preview", "us-barcode-scanner"); ?>
                                </label>
                            </td>
                            <td style="padding: 0 0 5px;">
                                <!-- checkbox -->
                                <?php $checked = $field["show_on_mobile_preview"] == 1 ? ' checked=checked ' : ''; ?>
                                <input type="checkbox" class="usbs_field_show_on_mobile_preview" <?php esc_html_e($checked, 'us-barcode-scanner'); ?> data-fid="<?php echo esc_attr($field["id"]); ?>"
                                    onchange="WebbsSettingsCheckboxChange(`#bs-settings-fields-tab .show_on_mobile_preview input[data-fid='<?php echo esc_attr($field['id']); ?>']`, this.checked ? '1' : '0')" />
                                <input type="hidden" name="fields[<?php echo esc_attr($field["id"]); ?>][show_on_mobile_preview]" value="<?php echo $checked ? esc_attr("1") : esc_attr("0"); ?>" data-fid="<?php echo esc_attr($field["id"]); ?>" />
                            </td>
                        </tr>

                        <tr>
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <?php echo esc_html__("Field label", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 0;">
                                <input type="text" class="usbs_field_label" name="fields[<?php echo esc_attr($field["id"]); ?>][field_label]" value="<?php echo esc_attr($field["field_label"]); ?>" style="width: 177px;" />
                            </td>
                        </tr>
                        <tr>
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <?php echo esc_html__("Meta name", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 0;">
                                <input type="text" class="usbs_field_name" name="fields[<?php echo esc_attr($field["id"]); ?>][field_name]" value="<?php echo esc_attr($field["field_name"]); ?>" style="width: 177px;" />
                                <button type="button" class="cf_check_name">Check</button>
                                <div style="display: inline-block; position: relative; width: 1px;">
                                    <span class="cf_check_name_result"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <?php echo esc_html__("Taxonomy", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 0;">
                                <input type="text" class="usbs_taxonomy" name="fields[<?php echo esc_attr($field["id"]); ?>][taxonomy_field_name]" value="<?php echo esc_attr($field["field_name"]); ?>" style="width: 177px;" />
                            </td>
                        </tr>
                        <tr>
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <?php echo esc_html__("Term", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 0;">
                                <input type="text" class="usbs_term" name="fields[<?php echo esc_attr($field["id"]); ?>][term]" value="<?php echo esc_attr($field["term"]); ?>" style="width: 177px;" />
                            </td>
                        </tr>
                        <tr class="global_attribute">
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <?php echo esc_html__("Global attribute", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 0;">
                                <select class="usbs_field_global_attribute" name="fields[<?php echo esc_attr($field["id"]); ?>][attribute_id]" style="width: 177px;">
                                    <?php foreach ($globalAttributes as $key => $value): ?>
                                        <option value="<?php echo esc_attr($value->attribute_id) ?>" <?php echo $field["attribute_id"] == $value->attribute_id ? wp_kses_post("selected='selected'") : ""; ?>><?php echo esc_html($value->attribute_label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr class="type_select">
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <?php echo esc_html__("Options", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 5px 0;">
                                <div class="type_select_options">
                                    <?php $options = isset($field["options"]) && $field["options"] ? @json_decode($field["options"], false) : null; ?>
                                    <?php if ($options): ?>
                                        <?php $optionIndex = 0; ?>
                                        <?php foreach ($options as $key => $value): ?>
                                            <div class="type_select_option">
                                                <input type="text" name="fields[<?php echo esc_attr($field["id"]); ?>][options][<?php echo esc_attr($optionIndex); ?>][key]" value="<?php echo esc_attr($key); ?>" />
                                                <input type="text" name="fields[<?php echo esc_attr($field["id"]); ?>][options][<?php echo esc_attr($optionIndex); ?>][value]" value="<?php echo esc_attr($value); ?>" />
                                                <span class="type_select_option_remove">✖</span>
                                            </div>
                                            <?php $optionIndex++; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <span class="type_select_option_add">+ <?php echo esc_html__("Add new", "us-barcode-scanner"); ?></span>
                            </td>
                        </tr>
                        <?php  ?>
                        <?php  ?>
                        <tr>
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <?php echo esc_html__("Button width", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 0;">
                                <input class="button_width" style="width: 100px;" value="<?php echo esc_attr($field["button_width"]); ?>" name="fields[<?php echo esc_attr($field["id"]); ?>][button_width]" /> %
                            </td>
                        </tr>
                        <tr>
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                <?php echo esc_html__("Button's JS", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 0;">
                                <button type="button" class="edit_java_script"><?php echo esc_html__("Edit JavaScript", "us-barcode-scanner"); ?></button>
                                <?php
                                $allowed_tags = wp_kses_allowed_html('post');
                                $button_js = isset($field["button_js"]) && !empty($field["button_js"]) ? $field["button_js"] : '';
                                ?>
                                <div class="edit_java_script_modal" style="display: none;">
                                    <div>
                                        <div onmousedown="event.stopPropagation()">
                                            <?php echo "Get product details"; ?><br />
                                            <?php echo "const product = window.BarcodeScannerApp.productTab.getCurrentProduct();"; ?>
                                            <br /><br />
                                            <?php echo "Set and save field for product"; ?><br />
                                            <?php echo 'window.BarcodeScannerApp.productTab.setProductMeta({ "_sku", "NEW_SKU" });'; ?>
                                            <br /><br />
                                            <?php echo "Display prompt popup"; ?><br />
                                            <?php echo 'const value = await window.BarcodeScannerApp.modals.prompt({ field_type: "number", title: "Prompt title" });'; ?>
                                            <br /><br />
                                            <?php echo "Open URL in browser or new tab"; ?><br />
                                            <?php echo 'window.openBrowser("https://www.google.com");'; ?>
                                        </div>
                                        <div>
                                            <textarea class="button_js" rows="10" cols="70" name="fields[<?php echo esc_attr($field["id"]); ?>][button_js]"><?php echo $button_js ? wp_kses($button_js, $allowed_tags) : "" ?></textarea>
                                        </div>
                                        <div style="display: flex; justify-content: flex-end;">
                                            <button type="button" class="edit_java_script_modal_close"><?php echo esc_html__("Close", "us-barcode-scanner"); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                                        <?php echo esc_html__("Height", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 0;">
                                <input type="text" class="usbs_field_height" name="fields[<?php echo esc_attr($field["id"]); ?>][field_height]" value="<?php echo esc_attr($field["field_height"]); ?>" style="width: 100px;" /> px
                            </td>
                        </tr>
                        <tr>
                            <td scope="row" style="text-align:left; padding: 0 10px 0 10px; width: 110px; box-sizing: border-box;">
                                                        <?php echo esc_html__("Label width", "us-barcode-scanner"); ?>
                            </td>
                            <td style="padding: 0;">
                                <input type="number" class="usbs_label_width" name="fields[<?php echo esc_attr($field["id"]); ?>][label_width]" value="<?php echo esc_attr($field["label_width"]); ?>" style="width: 100px" /> %
                            </td>
                        </tr>
                    </table>

                    <div style="height: 30px;"></div>
                    <span class="dashicons dashicons-trash settings_field_remove" title="<?php echo esc_html__("Remove field", "us-barcode-scanner"); ?>" data-fid="<?php echo esc_attr($field["id"]); ?>"></span>
                </div>
            </div>
        </div>
    </td>
</tr>