<?php 
$tabsList = array(
    'inventory' => array('label' => esc_html__("Manage existing products", "us-barcode-scanner"), 'parent' => 'inventory', 'group' => ''),
    'newprod' => array('label' => esc_html__("Create new products", "us-barcode-scanner"), 'parent' => '', 'group' => ''),
    'prod_search_action' => array('label' => esc_html__("Product search auto action", "us-barcode-scanner"), 'parent' => '', 'group' => 'inventory', 'tooltip' => esc_html__("Product auto-action allows to initiate action for the found product, e.g. increase/decrease stock quantity automatically.", "us-barcode-scanner")),
    'orders' => array('label' => esc_html__("Manage existing orders", "us-barcode-scanner"), 'parent' => 'orders', 'group' => ''),
    'onlymy' => array('label' => esc_html__('Display only "My Orders"', "us-barcode-scanner"), 'parent' => '', 'group' => 'orders', 'tooltip' => esc_html__("Display and manage orders only created or assigned to the current user.", "us-barcode-scanner")),
    'link_current_user' => array('label' => esc_html__("Use current user data for new order", "us-barcode-scanner"), 'parent' => '', 'group' => '', 'tooltip' => esc_html__("Link the current user to the newly created order.", "us-barcode-scanner")),
    'order_search_action' => array('label' => esc_html__("Order search auto action", "us-barcode-scanner"), 'parent' => '', 'group' => 'orders', 'tooltip' => esc_html__("Order auto-action allows to initiate action for the found order, e.g. change order status automatically.", "us-barcode-scanner")),
    'show_prices' => array('label' => esc_html__("Show order prices", "us-barcode-scanner"), 'parent' => '', 'group' => 'orders', 'tooltip' => esc_html__("Display prices for existing orders and prices of the purchased items.", "us-barcode-scanner")),
    'order_edit' => array('label' => esc_html__("Allow to edit data of existing orders", "us-barcode-scanner"), 'parent' => '', 'group' => 'orders'),
    'order_edit_address' => array('label' => esc_html__("Edit order billing/shipping data", "us-barcode-scanner"), 'parent' => '', 'group' => ''),
    'cart' => array('label' => esc_html__("Create new orders", "us-barcode-scanner"), 'parent' => 'order', 'group' => ''),
    'edit_prices' => array('label' => esc_html__("Manage new order prices", "us-barcode-scanner"), 'parent' => '', 'group' => 'order', 'tooltip' => esc_html__("Disable this option to make new order prices read-only.", "us-barcode-scanner")),
    'linkcustomer' => array('label' => esc_html__("Assign customer to order", "us-barcode-scanner"), 'parent' => '', 'group' => 'order', 'tooltip' => esc_html__("Allows to link a customer to the order.", "us-barcode-scanner")),
    'frontend' => array('label' => esc_html__("Display frontend popup", "us-barcode-scanner"), 'parent' => '', 'group' => '', 'tooltip' => esc_html__('Allows to give the access to the barcode-scanner without having access to wp admin panel. See details in the "Front-end popup" tab.', "us-barcode-scanner")),
    'app_qty_plus' => array('label' => esc_html__('App, show "Qty +1" button', "us-barcode-scanner"), 'parent' => '', 'group' => ''),
    'app_qty_minus' => array('label' => esc_html__('App, show "Qty -1" button', "us-barcode-scanner"), 'parent' => '', 'group' => ''),
    'app_save_list' => array('label' => esc_html__('App, show "Save/List" buttons', "us-barcode-scanner"), 'parent' => '', 'group' => ''),
    'plugin_settings' => array('label' => esc_html__("Settings page", "us-barcode-scanner"), 'parent' => '', 'group' => ''),
    'plugin_logs' => array('label' => esc_html__("Logs page", "us-barcode-scanner"), 'parent' => '', 'group' => ''),
);
?>
<form id="bs-settings-permissions-tab" method="POST" action="<?php echo esc_url($actualLink); ?>">
    <input type="hidden" name="tab" value="permissions" />
    <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>" />
    <table class="form-table">
        <tbody>
            <tr>
                <th scope="row" colspan="2" style="padding-bottom: 0;">
                    <b><?php echo esc_html__("Tabs permissions:", "us-barcode-scanner"); ?></b>
                <th>
            </tr>
            <tr>
                <td colspan="2">
                    <!-- roles -->
                    <table class="bs-settings-roles-list">
                        <tr>
                            <td><?php echo esc_html__("Role", "us-barcode-scanner"); ?></td>

                            <!--  -->
                            <?php foreach ($settings->getRoles() as $key => $role) : ?>
                                <td><?php echo esc_html($role["name"]); ?></td>
                            <?php endforeach; ?>
                        </tr>

                        <?php foreach ($tabsList as $permissionKey => $permission) : ?>
                            <tr>
                                <td>
                                    <?php echo esc_html($permission['label']); ?>
                                    <?php if (isset($permission['tooltip'])) : ?>
                                        <span class="dashicons dashicons-info" title="<?php echo esc_attr($permission['tooltip']); ?>" style="color: #717171;"></span>
                                    <?php endif; ?>
                                </td>

                                <?php foreach ($settings->getRoles() as $key => $role) : ?>
                                    <?php $permissions = $settings->getRolePermissions($key); ?>
                                    <td style="text-align: center;" data-role="<?php echo esc_attr($key); ?>" data-permission="<?php echo esc_attr($permissionKey); ?>">
                                        <?php
                                        if ($permissions && isset($permissions[$permissionKey]) && $permissions[$permissionKey]) $checked = ' checked=checked ';
                                        else $checked = '';

                                        $parent = $permission['parent'] ? 'parent="' . $permission['parent'] . '"' : '';
                                        $group = $permission['group'] ? 'group="' . $permission['group'] . '"' : '';
                                        ?>
                                        <input type="hidden" name="rolesPermissions[<?php echo esc_attr($key); ?>][<?php echo esc_attr($permissionKey); ?>]" value="0" />
                                        <input type="checkbox" name="rolesPermissions[<?php echo esc_attr($key); ?>][<?php echo esc_attr($permissionKey); ?>]" value="1" <?php esc_html_e($checked, 'us-barcode-scanner'); ?> <?php echo esc_attr($parent); ?> <?php echo esc_attr($group); ?> />
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="submit">
        <input type="submit" class="button button-primary" value="<?php echo esc_html__("Save Changes", "us-barcode-scanner"); ?>">
    </div>
</form>

<script>
    jQuery(document).ready(() => {
        jQuery(".bs-settings-roles-list tr input[type='checkbox']").change((e) => {
            const parent = jQuery(e.target).attr("parent");
            const group = jQuery(e.target).attr("group");
            const status = jQuery(e.target).is(":checked");

            const role = jQuery(e.target).closest("td").attr("data-role");
            const permission = jQuery(e.target).closest("td").attr("data-permission");

            if (parent && status) {
                jQuery(e.target).closest("table").find("td[data-role='" + role + "'] input[type='checkbox'][group='" + parent + "']").removeAttr("disabled");
            } else {
                jQuery(e.target).closest("table").find("td[data-role='" + role + "'] input[type='checkbox'][group='" + parent + "']").prop("checked", false);
                jQuery(e.target).closest("table").find("td[data-role='" + role + "'] input[type='checkbox'][group='" + parent + "']").attr("disabled", "disabled");
            }
        });

        jQuery(".bs-settings-roles-list tr input[type='checkbox']:not([data-need-permissions])").change();

        <?php
        ?>
    });
</script>