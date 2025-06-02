<?php
$tabPrintingFields = array(
    array(
        "label" => esc_html__("Press \"CREATE\" label button automatically", "us-barcode-scanner"),
        "type" => "checkbox",
        "fieldName" => "uslpBtnAutoCreate",
        "defaultValue" => "off",
    ),
    array(
        "label" => esc_html__("Use \"Receipt\" button for label printing plugin", "us-barcode-scanner"),
        "type" => "checkbox",
        "fieldName" => "uslpUseReceiptToPrint",
        "defaultValue" => "off",
    ),
);
?>
<style>
</style>
<form id="bs-settings-label-printing-tab" method="POST" action="<?php echo esc_url($actualLink); ?>">
    <input type="hidden" name="tab" value="label-printing" />
    <input type="hidden" name="storage" value="table" />
    <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>" />
    <table class="form-table">
        <tbody>
            <?php
            $frontendIntegration = $settings->getField("general", "frontendIntegration", "");
            $allowFrontendShortcodes = $settings->getField("general", "allowFrontendShortcodes", "");
            ?>
            <?php foreach ($tabPrintingFields as $field): ?>
                <?php
                $fieldName = $field["fieldName"];
                $fieldValue = $settings->getSettings($fieldName);
                $fieldValue = $fieldValue === null ? $field["defaultValue"] : $fieldValue->value;
                ?>
                <tr id="<?php echo esc_attr($fieldName); ?>">
                    <th scope="row"><?php echo esc_html($field["label"]); ?></th>
                    <td>
                        <?php if ($field["type"] == "checkbox"): ?>
                            <label>
                                <?php
                                if ($fieldValue === "on") {
                                    $checked = ' checked=checked ';
                                } else {
                                    $checked = '';
                                }
                                ?>
                                <input type="checkbox" <?php esc_html_e($checked, 'us-barcode-scanner'); ?> onchange="WebbsSettingsCheckboxChange(`#<?php echo esc_attr($fieldName); ?> input[name='<?php echo esc_attr($fieldName); ?>']`,this.checked ? 'on' : 'off')" />
                                <input type="hidden" name="<?php echo esc_attr($fieldName); ?>" value="<?php echo esc_attr($checked) ? "on" : "off"; ?>" />
                                <?php echo esc_html__("Enable", "us-barcode-scanner"); ?>
                                <i></i>
                            </label>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="submit">
        <input type="submit" class="button button-primary" value="<?php echo esc_html__("Save Changes", "us-barcode-scanner"); ?>">
    </div>
</form>