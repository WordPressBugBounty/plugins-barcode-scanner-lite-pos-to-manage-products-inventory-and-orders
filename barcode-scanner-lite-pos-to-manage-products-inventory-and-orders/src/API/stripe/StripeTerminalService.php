<?php

namespace UkrSolution\BarcodeScanner\API\stripe;

require_once __DIR__ . '/stripe-php-master/init.php';

use Stripe\Stripe;
use Stripe\Terminal\ConnectionToken;
use Stripe\PaymentIntent;
use UkrSolution\BarcodeScanner\API\classes\LogsHelper;
use UkrSolution\BarcodeScanner\features\settings\Settings;
use WP_REST_Request;

class StripeTerminalService
{
    private static $validateKey = "";
    private static $apiVersion = "2023-10-16";

    private static function init()
    {
        try {
            if (self::$validateKey) {
                Stripe::setApiKey(self::$validateKey);
                Stripe::setApiVersion(self::$apiVersion);
            }
            else {
                $settings = new Settings();
                $field = $settings->getSettings("stripeApiSecretKey");
                $value = $field === null ? "" : $field->value;
                $value = $value ? $value : "";

                Stripe::setApiKey($value);
                Stripe::setApiVersion(self::$apiVersion);
            }

        } catch (\Throwable $th) {
            return array("error" => $th->getMessage());
        }
    }

    private static function createConnectionToken()
    {
        try {
            self::init();

            $token = ConnectionToken::create();

            LogsHelper::add("create token: {$token->secret}");

            return array('secret' => $token->secret);
        } catch (\Throwable $th) {
            return array("error" => $th->getMessage());
        }
    }

    private static function createPaymentIntent($amount, $currency = 'eur')
    {
        self::init();

        try {
            LogsHelper::add("create PI: {$amount} {$currency}");

            $intent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => $currency,
                'payment_method_types' => ['card_present'],
                'capture_method' => 'automatic',
            ]);

                        LogsHelper::add("PI id: {$intent->id}");

                        $intent = PaymentIntent::retrieve($intent->id);

            LogsHelper::add("PI status: {$intent->status}");

            return array(
                'id' => $intent->id,
                'client_secret' => $intent->client_secret,
                'status' => $intent->status,
            );
        } catch (\Throwable $th) {
            return array("error" => $th->getMessage());
        }
    }

    public static function stripeFetchConnectionToken(WP_REST_Request $request)
    {
        $payload = $request->get_param("payload");
        $secretKeyToValidate = isset($payload["key"]) ? $payload["key"] : null;

        if ($secretKeyToValidate) {
            self::$validateKey = $secretKeyToValidate;
        }

        return rest_ensure_response(self::createConnectionToken());
    }

    public static function stripeCreatePaymentIntent(WP_REST_Request $request)
    {
        $payload = $request->get_param("payload");

        $amount = isset($payload["amount"]) ? $payload["amount"] : 0;
        $currency = isset($payload["currency"]) ? $payload["currency"] : 0;

        return rest_ensure_response(self::createPaymentIntent($amount, $currency));
    }
}
