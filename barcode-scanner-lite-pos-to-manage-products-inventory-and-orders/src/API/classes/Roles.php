<?php

namespace UkrSolution\BarcodeScanner\API\classes;

class Roles
{
    public function __construct()
    {
        $subscriber_role = get_role('subscriber');

        $capabilities = $subscriber_role && $subscriber_role->capabilities ? $subscriber_role->capabilities : array();

        $custom_capabilities = array();

        add_role('barcode_scanner_front_end', "Barcode Scanner (Front-End)", array_merge($capabilities, $custom_capabilities));
    }
}
