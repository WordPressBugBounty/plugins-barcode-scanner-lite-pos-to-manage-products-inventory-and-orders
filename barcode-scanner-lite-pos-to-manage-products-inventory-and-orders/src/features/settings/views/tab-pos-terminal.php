<?php

use UkrSolution\BarcodeScanner\API\classes\SearchFilter;
use UkrSolution\BarcodeScanner\API\classes\WPML;
use UkrSolution\BarcodeScanner\features\settings\SettingsHelper;
?>
<form class="bs-settings-input-conditions" id="bs-pos-terminal-tab" method="POST"
    action="<?php echo esc_url($actualLink); ?>">
    <input type="hidden" name="tab" value="pos-terminal" />
    <input type="hidden" name="storage" value="table" />
    <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>" />

    <div style="padding: 20px 20px 0 0; font-size: 14px;">
        <?php echo esc_html__("Only android based stripe terminals are supported, like S710, WisePOS E, Verifon, etc.", "us-barcode-scanner"); ?>
    </div>

    <table class="form-table">
        <tbody>
            <tr class="usbs-section-label">
                <td>
                    <h2 style="padding-top: 10px;"><?php echo esc_html__("Stripe", "us-barcode-scanner"); ?></h2>
                </td>
            </tr>
            <tr id="disabled_variations_products">
                <th scope="row">
                    <?php echo esc_html__('POS terminal', "us-barcode-scanner"); ?>
                </th>
                <td>
                    <label>
                        <?php
                        $defaultValue = $settings->getSettings("stripeApiEnabled");
                        $defaultValue = $defaultValue === null ? 'on' : $defaultValue->value;
                        $checked = $defaultValue !== "off" ? ' checked=checked ' : '';
                        ?>
                        <input type="checkbox" <?php esc_html_e($checked, 'us-barcode-scanner'); ?>
                            data-main="stripeApiEnabled" onchange="WebbsSettingsCheckboxChange(`#disabled_variations_products
                        input[name='stripeApiEnabled']`,this.checked ? 'on' : 'off')" />
                        <input type="hidden" name="stripeApiEnabled" value="<?php echo $checked ? "on" : "off"; ?>" />
                        <?php echo esc_html__("Enable", "us-barcode-scanner"); ?> <span
                            class="usbs-option-notice"></span>
                    </label>
                </td>
            </tr>
            <!-- secret key -->
            <tr>
                <th scope="row">
                    <?php echo esc_html__("Stripe secret key", "us-barcode-scanner"); ?>
                </th>
                <td>
                    <label>
                        <?php
                        $field = $settings->getSettings("stripeApiSecretKey");
                        $value = $field === null ? "" : $field->value;
                        $value = $value ? $value : "";
                        ?>
                        <input type="text" name="stripeApiSecretKey" value="<?php echo esc_html($value); ?>" />
                        <button type="button" id="pos-terminal-stripe-validate">
                            <?php echo esc_html__("Validate", "us-barcode-scanner"); ?>
                        </button>
                    </label>
                    <div id="stripeTerminalMessage"></div>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <?php echo esc_html__("Default terminal", "us-barcode-scanner"); ?>
                </th>
                <td>
                    <?php
                    $field = $settings->getSettings("stripeDefaultTerminalId");
                    $value = $field === null ? "" : $field->value;
                    $value = $value ? $value : "";
                    ?>
                    <div id="stripeTerminalsList">
                        <select disabled>
                            <option><?php echo esc_html__('Not selected', 'us-barcode-scanner'); ?></option>
                        </select>
                    </div>                    
                    <script>window.stripeDefaultTerminalId = '<?php echo esc_html($value); ?>'</script>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="submit">
        <input type="submit" class="button button-primary"
            value="<?php echo esc_html__("Save Changes", "us-barcode-scanner"); ?>">
    </div>
</form>
<script>
    jQuery(document).ready(function () {
    });
</script>