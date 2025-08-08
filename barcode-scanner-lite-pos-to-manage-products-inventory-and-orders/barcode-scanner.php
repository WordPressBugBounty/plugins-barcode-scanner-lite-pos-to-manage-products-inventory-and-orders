<?php
/**
 * Plugin Name: Barcode Scanner with Inventory & Order Manager - (business)
 * Description: Scan barcodes to find & manage inventory and orders.
 * Text Domain: us-barcode-scanner
 * Version: 1.10.2
 * Build: 1754656173744
 * Author: UkrSolution
 * Plugin URI: https://www.ukrsolution.com/Wordpress/WooCommerce-Barcode-QRCode-Scanner-Reader
 * Author URI: http://www.ukrsolution.com
 * License: GPL2
 * WC requires at least: 2.0.0
 * -WC tested up to: 10.0.*
 */

use UkrSolution\BarcodeScanner\Database;
use UkrSolution\BarcodeScanner\features\mobile\MobileRouter;
use UkrSolution\BarcodeScanner\features\settings\Settings;

if (!defined('ABSPATH')) {
    exit;
}

include_once ABSPATH . 'wp-admin/includes/plugin.php';

if (file_exists(__DIR__ . '/src/PaymentCashCashier.php')) {
    include_once __DIR__ . '/src/PaymentCashCashier.php';
}

if (!is_plugin_active(plugin_basename(__FILE__))) {
    $activePlugins = is_multisite() ? get_site_option('active_sitewide_plugins') : get_option('active_plugins');

    foreach ($activePlugins as $sitewideActivePlugin => $activePlugin) {
        if (preg_match('/barcode-scanner-(basic|business|premium|mobile)\/barcode-scanner.php/', $activePlugin, $m)) {
            @deactivate_plugins($activePlugin);
            @activate_plugin(plugin_basename(__FILE__));
            return;
        }
    }
}



define('USBS_PLUGIN_BASE_URL', plugin_dir_url(__FILE__));
define('USBS_PLUGIN_BASE_PATH', plugin_dir_path(__FILE__));
define('USBS_PLUGIN_BASE_NAME', plugin_basename(__FILE__));

add_filter('wpss_misc_form_spam_check_bypass', function($data) {
    $jsonPost = file_get_contents('php://input');
    return $jsonPost ? true : $data;
}, 1, 1);

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook(__FILE__, function ($networkWide) {
    Database::setupTables($networkWide);
});

add_action('wpmu_new_blog', function ($blogId, $userId, $domain, $path, $siteId, $meta) {
    if (is_plugin_active_for_network(plugin_basename(__FILE__))) {
        switch_to_blog($blogId);
        Database::createTables();
        restore_current_blog();
    }
}, 10, 6);

add_action('init', function () {
    $pluginRelPath = basename(dirname(__FILE__)) . '/languages';
    load_plugin_textdomain('us-barcode-scanner', false, $pluginRelPath);
}, 1);


add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $url = get_admin_url() . "admin.php?page=barcode-scanner-settings";
    $settings_link = '<a href="' . $url . '">Settings</a>';
    $links[] = $settings_link;

    return $links;
});


if(function_exists("wp_get_upload_dir")) {
    try {
        $dir = wp_get_upload_dir();
        $pathUpload = $dir["basedir"]  . '/barcode-scanner/';
        $pathApi = $dir["basedir"]  . '/barcode-scanner/api.php';
        $pathExample = $dir["basedir"]  . '/barcode-scanner/api-example.php';

        if (!file_exists($pathUpload)) {
            wp_mkdir_p($pathUpload);
        }

        if (file_exists($pathApi)) {
            include_once($pathApi);
        } 
        else if(function_exists("copy") && file_exists(__DIR__ . "/api-example.php") && !file_exists($pathExample)){
            copy(__DIR__ . "/api-example.php", $pathExample);
        }
    } catch (\Throwable $th) {
    }
}

if (file_exists(get_template_directory() . '/barcode-scanner/api.php')) {
    include_once(get_template_directory() . '/barcode-scanner/api.php');
}

new UkrSolution\BarcodeScanner\Core();