<?php

namespace UkrSolution\BarcodeScanner\features\sounds;

use UkrSolution\BarcodeScanner\features\settings\Settings;

class Sounds
{
    private $list = array(
        "fail" => USBS_PLUGIN_BASE_URL . "assets/sounds/fail-v2.mp3",
        "increase" => USBS_PLUGIN_BASE_URL . "assets/sounds/increase.mp3",
        "decrease" => USBS_PLUGIN_BASE_URL . "assets/sounds/decrease.mp3",
        "ffEnd" => USBS_PLUGIN_BASE_URL . "assets/sounds/notification-sound-3-262896.mp3",
    );

    public function getList()
    {
        $settings = new Settings();
        $sounds = $this->list;

        $sound_increase = $settings->getSettings("sound_increase");
        $sound_decrease = $settings->getSettings("sound_decrease");
        $sound_fail = $settings->getSettings("sound_fail");
        $sound_ffEnd = $settings->getSettings("sound_ffEnd");

        if ($sound_increase && $sound_increase->value) {
            $sounds["increase"] = $sound_increase->value;
        }

        if ($sound_decrease && $sound_decrease->value) {
            $sounds["decrease"] = $sound_decrease->value;
        }

        if ($sound_fail && $sound_fail->value) {
            $sounds["fail"] = $sound_fail->value;
        }

        if ($sound_ffEnd && $sound_ffEnd->value) {
            $sounds["ffEnd"] = $sound_ffEnd->value;
        }

        return $sounds;
    }
}
