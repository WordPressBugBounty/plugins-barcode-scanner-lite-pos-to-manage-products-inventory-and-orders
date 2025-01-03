<?php

use UkrSolution\BarcodeScanner\features\settings\SettingsHelper;

$subTabUrl = get_admin_url() . "admin.php?page=barcode-scanner-settings&tab=fields";
$deviceActive = isset($_GET["sub"]) ? sanitize_text_field($_GET["sub"]) : "desktop";
$roleActive = "default";
$roleActiveName = "";

$fieldsRoles = SettingsHelper::getRolesList();
$globalAttributes = $managementActions->getGlobalAttributes(null, true);
$globalAttributes = $globalAttributes && isset($globalAttributes["attributes"]) ? $globalAttributes["attributes"] : array();

if ($roleActive) {
    foreach ($fieldsRoles as $role => $value) {
        if ($roleActive == $role) {
            $roleActiveName = $value['name'];
            break;
        }
    }
}
?>
<div class="usbs-subtubs">
    <div>
        <select id="usbs-role-selector">
            <option value="default" <?php echo $roleActive == 'default' ? esc_attr("selected") : "" ?>><?php echo esc_html('Default', "us-barcode-scanner"); ?> (<?php echo esc_html('On', "us-barcode-scanner"); ?>)</option>
            <?php foreach ($fieldsRoles as $role => $value) : ?>
                <option value="<?php echo esc_attr($role) ?>" data-bs_fields="<?php echo isset($value['bs_fields']) ? esc_attr($value['bs_fields']) : '' ?>" <?php echo $roleActive == $role ? esc_attr("selected") : "" ?>>
                    <?php echo $value['name']; ?>
                    <?php if (isset($value['bs_fields'])) : ?>
                        (<?php echo esc_html('On', "us-barcode-scanner"); ?>)
                    <?php endif; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>&nbsp;</div>
    <div>
        <select id="usbs-device-selector">
            <option value="desktop" <?php echo $deviceActive == "desktop" ? esc_attr("selected") : "" ?>><?php echo esc_html('Desktop', "us-barcode-scanner"); ?></option>
            <option value="mobile" <?php echo $deviceActive == "mobile" ? esc_attr("selected") : "" ?>><?php echo esc_html('Mobile', "us-barcode-scanner"); ?></option>
        </select>
    </div>
</div>

<script>
    const bsFieldsRoles = <?php echo json_encode($fieldsRoles); ?>;
</script>
<?php
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelector = document.getElementById('usbs-role-selector');
        const deviceSelector = document.getElementById('usbs-device-selector');
        const loadDefaultSettingsSelector = document.getElementById('usbs-fields-load-default-settings');
        const removeSettingsSelector = document.getElementById('usbs-fields-remove-settings');

        let previousRoleValue = roleSelector.value;
        let previousDeviceValue = deviceSelector.value;


        const decodeHtmlEntities = (text) => {
            let textarea = document.createElement('textarea');
            textarea.innerHTML = text;
            return textarea.value;
        }

        function updateURL() {
            const roleValue = roleSelector.value;
            const deviceValue = deviceSelector.value;
            const role = bsFieldsRoles[roleValue];

            const targetUrl = window.usbs.urlSettings + '&tab=fields&role=' + roleValue + '&sub=' + deviceValue;

            if (role && role != 'default') {
                alert("Fields customization for different roles available only in the Premium plan.\nPlease, update you plan in your account on www.ukrsolution.com");
                roleSelector.value = previousRoleValue;
                deviceSelector.value = previousDeviceValue;
            } else if (role && !role.bs_fields && previousRoleValue != roleValue) {
                if (confirm(decodeHtmlEntities(`<?php echo esc_html__('Do you really want to create separate Product field settings for role "%s"?', "us-barcode-scanner"); ?>`).replace('%s', role.name))) {
                    window.location.href = targetUrl + '&init=1';
                    return;
                } else {
                    roleSelector.value = previousRoleValue;
                    deviceSelector.value = previousDeviceValue;
                    return;
                }
            }

            if ((roleValue != previousRoleValue || deviceValue != previousDeviceValue) && (!role || role == 'default')) window.location.href = targetUrl;
        }

        roleSelector.addEventListener('change', updateURL);
        deviceSelector.addEventListener('change', updateURL);
    });
</script>
<?php
?>

<form id="bs-settings-fields-tab" method="POST" action="<?php echo esc_url($actualLink); ?>">
    <input type="hidden" name="tab" value="fields" />
    <input type="hidden" name="sub" value="<?php echo esc_attr($deviceActive); ?>" />
    <input type="hidden" name="role" value="<?php echo esc_attr($roleActive); ?>" />
    <input type="hidden" name="storage" value="table" />
    <input type="hidden" name="nonce" value="<?php echo esc_attr($nonce); ?>" />
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; max-width: 1190px;">
        <div style="padding: 0px 0 0 10px;"><?php echo esc_html('Here you can add/edit/remove fields which displays in the "Inventory" tab (in popup).', "us-barcode-scanner"); ?></div>
    </div>
    <div style="display: flex; padding: 25px 0 0 10px; flex-flow: row wrap;">
        <?php
        if ($deviceActive == "mobile") {
            require_once __DIR__ . "/fields/_mobile.php";
        } else {
            require_once __DIR__ . "/fields/_web.php";
        }
        ?>
    </div>

    <div class="submit" style="gap: 30px; display: flex; align-items: center; flex-wrap: wrap;">
        <input type="submit" class="button button-primary" value="<?php echo esc_html__("Save Changes", "us-barcode-scanner"); ?>">

        <?php if ($roleActive && $roleActive != 'default') : ?>
            <?php $url = admin_url('/admin.php?page=barcode-scanner-settings&tab=fields&role=' . $roleActive . '&sub=' . $deviceActive . '&ld=1'); ?>
            <div style="margin-left: 20px;"><a id="usbs-fields-load-default-settings" href="<?php echo esc_url($url); ?>&sub=desktop"><?php echo esc_html__('Load default field settings', "us-barcode-scanner"); ?></a></div>
            <?php $url = admin_url('/admin.php?page=barcode-scanner-settings&tab=fields&role=' . $roleActive . '&sub=' . $deviceActive . '&rm=1'); ?>
            <div><a id="usbs-fields-remove-settings" href="<?php echo esc_url($url); ?>&sub=desktop"><?php echo sprintf(esc_html__('Remove setting for "%s" role', "us-barcode-scanner"), esc_html($roleActiveName ? $roleActiveName : $roleActive)); ?></a></div>
        <?php endif; ?>
    </div>
</form>

<script>
    let codeMirrorInstances = [];

    jQuery(document).ready(() => {
        console.log("ready");

        jQuery('.settings_field_settings').click((e) => {
            const section = jQuery(e.target).attr('data-section');
            jQuery('div[data-section-content="' + section + '"]').slideToggle();
        });

        jQuery(document).on("click", '.edit_java_script', (e) => {
            const editJavaScriptModalEl = jQuery(e.target).closest(".settings_field_body").find(".edit_java_script_modal");
            editJavaScriptModalEl.removeAttr("style");

            setTimeout(() => {
                const settingsFieldBody = e.target.closest('.settings_field_body');
                const textarea = settingsFieldBody ? settingsFieldBody.querySelector('.edit_java_script_modal .button_js') : null;
                reinitializeCodeMirror(textarea);

                const codeMirrorInstance = editJavaScriptModalEl.find('.CodeMirror')[0].CodeMirror;

                if (codeMirrorInstance) codeMirrorInstance.focus();
            }, 0);
        });

        jQuery(document).on("click", '.edit_java_script_modal_close', (e) => {
            const editJavaScriptModalEl = jQuery(e.target).closest(".settings_field_body").find(".edit_java_script_modal");
            editJavaScriptModalEl.css("display", "none");
        });

        jQuery(document).on("click", '.edit_java_script_modal', (e) => {
            e.stopPropagation();
            e.preventDefault();
        });

        jQuery(document).on("mousedown", '.edit_java_script_modal', (e) => {
            e.stopPropagation();
            e.preventDefault();
        });

        jQuery(document).on("mousemove", '.edit_java_script_modal', (e) => {
            e.stopPropagation();
            e.preventDefault();
        });

    });

    function reinitializeCodeMirror(textarea) {
        if (!textarea.classList.contains('initialized')) {
            const editor = CodeMirror.fromTextArea(textarea, {
                mode: 'javascript',
                lineNumbers: true
            });

            codeMirrorInstances.push(editor);

            textarea.classList.add('initialized');
        }
    }
</script>

<style>
    .edit_java_script_modal {
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: #0000004d;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
    }

    .edit_java_script_modal>div {
        background: #fff;
        border-radius: 4px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .edit_java_script_modal .CodeMirror {
        width: 600px;
        box-sizing: border-box;
    }
</style>