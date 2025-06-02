<?php

use UkrSolution\BarcodeScanner\features\settings\Settings;

$settings = new Settings();
$appLoginMethods = $settings->getSettings("appLoginMethods");
$defaultLoginMethod = $settings->getSettings("defaultLoginMethod");

echo json_encode(array(
    "success" => true,
    "blogName" => get_bloginfo("name"),
    "logoUrl" =>  esc_url(wp_get_attachment_url(get_theme_mod('custom_logo'))),
    "home" => get_home_url(),
    "appLoginMethods" => $appLoginMethods ? $appLoginMethods->value : "both",
    "defaultLoginMethod" => $defaultLoginMethod ? $defaultLoginMethod->value : "login_pass"
));
