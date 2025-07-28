<?php

namespace UkrSolution\BarcodeScanner\API\classes;

class PickList
{
    public static function getTemplate($post, $order, $settings)
    {
        $template = __DIR__ . "/../views/pick-list.php";

        if (file_exists($template)) {
            $pickListProductCode = $settings->getSettings("pickListProductCode");
            $pickListProductCode = $pickListProductCode === null ? "" : $pickListProductCode->value;

            ob_start();
            require $template;
            return base64_encode(ob_get_clean());
        } else {
            return "";
        }
    }
}
