<?php
$isMobile = false;
$orderField = "order";
$statusField = "status";

$beforeProductWidth = $settings->getSettings("beforeProductWidth");
$beforeProductWidth = $beforeProductWidth === null ? '235' : $beforeProductWidth->value;
$beforeProductRightWidth = $settings->getSettings("beforeProductRightWidth");
$beforeProductRightWidth = $beforeProductRightWidth === null ? '235' : $beforeProductRightWidth->value;
$afterProductWidth = $settings->getSettings("afterProductWidth");
$afterProductWidth = $afterProductWidth === null ? '235' : $afterProductWidth->value;
$afterProductRightWidth = $settings->getSettings("afterProductRightWidth");
$afterProductRightWidth = $afterProductRightWidth === null ? '235' : $afterProductRightWidth->value;
?>
<!-- Before products -->
<div style="margin-right: 20px; width: 380px;">
    <b><?php echo esc_html__("Before items (Top-Left):", "us-barcode-scanner"); ?></b>
    <button type="button" class="settings_field_settings" data-section="before-product-width"><?php echo esc_html__("Settings", "us-barcode-scanner"); ?></button>
    <button type="button" class="settings_order_field_add_new" data-ftype="_order" data-position="before-product"><?php echo esc_html__("Add new", "us-barcode-scanner"); ?></button>
    <!-- column settings -->
    <div style="margin: 15px 0 10px 0; display: none;" data-section-content="before-product-width">
        <?php echo esc_html__("Column width", "us-barcode-scanner"); ?>
        <input name="beforeProductWidth" type="number" min="230" style="width: 100px;" value="<?php echo esc_attr($beforeProductWidth); ?>" placeholder="320" />
        <?php echo esc_html__("px", "us-barcode-scanner"); ?>
    </div>

    <div style="height: 5px;"></div>
    <table class="form-table wrapper" data-position="before-product" style="min-height: 100px;">
        <tbody class="usbs_fields_list_sortable">
            <?php foreach ($interfaceData::getFields(false, "", false, $roleActive, false) as $field): ?>
                <?php if ($field["position"] == "before-product") {
                    require __DIR__ . "/order_field.php";
                } ?>
            <?php endforeach; ?>
            <!-- template -->
            <?php
            $field = array(
                "id" => 0,
                "field_name" => "",
                "term" => "",
                "field_label" => "New field",
                "label_position" => "left",
                "field_height" => "",
                "label_width" => "",
                "position" => "",
                "type" => "text",
                "order" => "",
                "order_mobile" => "",
                "status" => "1",
                "mobile_status" => "1",
                "show_in_create_order" => "0",
                "show_in_products_list" => "0",
                "read_only" => "0",
                "use_for_auto_action" => "0",
                "attribute_id" => "",
                "button_width" => "",
                "show_on_mobile_preview" => "0",
            );
            $rootClass = "new_field_template_order";
            require __DIR__ . "/order_field.php";
            $rootClass = "";
            ?>
            <!-- end template -->
        </tbody>
    </table>
</div>
<!-- end Before products -->

<!-- Before products -->
<div style="margin-right: 20px; width: 380px;">
    <b><?php echo esc_html__("Before items (Top-Right):", "us-barcode-scanner"); ?></b>
    <button type="button" class="settings_field_settings" data-section="before-product-right-width"><?php echo esc_html__("Settings", "us-barcode-scanner"); ?></button>
    <button type="button" class="settings_order_field_add_new" data-ftype="_order" data-position="before-product-right"><?php echo esc_html__("Add new", "us-barcode-scanner"); ?></button>
    <!-- column settings -->
    <div style="margin: 15px 0 10px 0; display: none;" data-section-content="before-product-right-width">
        <?php echo esc_html__("Column width", "us-barcode-scanner"); ?>
        <input name="beforeProductRightWidth" type="number" min="230" style="width: 100px;" value="<?php echo esc_attr($beforeProductRightWidth); ?>" placeholder="320" />
        <?php echo esc_html__("px", "us-barcode-scanner"); ?>
    </div>

    <div style="height: 5px;"></div>
    <table class="form-table wrapper" data-position="before-product-right" style="min-height: 100px;">
        <tbody class="usbs_fields_list_sortable">
            <?php foreach ($interfaceData::getFields(false, "", false, $roleActive, false) as $field): ?>
                <?php if ($field["position"] == "before-product-right") {
                    require __DIR__ . "/order_field.php";
                } ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- end Before products -->

<div style="width: 100%; height: 20px;"></div>

<!-- After products -->
<div style="margin-right: 20px; width: 380px;">
    <b><?php echo esc_html__("After items (Bottom-Left):", "us-barcode-scanner"); ?></b>
    <button type="button" class="settings_field_settings" data-section="after-product-width"><?php echo esc_html__("Settings", "us-barcode-scanner"); ?></button>
    <button type="button" class="settings_order_field_add_new" data-ftype="_order" data-position="after-product"><?php echo esc_html__("Add new", "us-barcode-scanner"); ?></button>
    <!-- column settings -->
    <div style="margin: 15px 0 10px 0; display: none;" data-section-content="after-product-width">
        <?php echo esc_html__("Column width", "us-barcode-scanner"); ?>
        <input name="afterProductWidth" type="number" min="230" style="width: 100px;" value="<?php echo esc_attr($afterProductWidth); ?>" placeholder="320" />
        <?php echo esc_html__("px", "us-barcode-scanner"); ?>
    </div>

    <div style="height: 5px;"></div>
    <table class="form-table wrapper" data-position="after-product" style="min-height: 100px;">
        <tbody class="usbs_fields_list_sortable">
            <?php foreach ($interfaceData::getFields(false, "", false, $roleActive, false) as $field): ?>
                <?php if ($field["position"] == "after-product") {
                    require __DIR__ . "/order_field.php";
                } ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- end After products -->

<!-- After products right -->
<div style="margin-right: 20px; width: 380px;">
    <b><?php echo esc_html__("After items (Bottom-Right):", "us-barcode-scanner"); ?></b>
    <button type="button" class="settings_field_settings" data-section="after-product-right-width"><?php echo esc_html__("Settings", "us-barcode-scanner"); ?></button>
    <button type="button" class="settings_order_field_add_new" data-ftype="_order" data-position="after-product-right"><?php echo esc_html__("Add new", "us-barcode-scanner"); ?></button>
    <!-- column settings -->
    <div style="margin: 15px 0 10px 0; display: none;" data-section-content="after-product-right-width">
        <?php echo esc_html__("Column width", "us-barcode-scanner"); ?>
        <input name="afterProductRightWidth" type="number" min="230" style="width: 100px;" value="<?php echo esc_attr($afterProductRightWidth); ?>" placeholder="320" />
        <?php echo esc_html__("px", "us-barcode-scanner"); ?>
    </div>

    <div style="height: 5px;"></div>
    <table class="form-table wrapper" data-position="after-product-right" style="min-height: 100px;">
        <tbody class="usbs_fields_list_sortable">
            <?php foreach ($interfaceData::getFields(false, "", false, $roleActive, false) as $field): ?>
                <?php if ($field["position"] == "after-product-right") {
                    require __DIR__ . "/order_field.php";
                } ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- end After products right -->