<?php


namespace Stripe\Treasury;

class CreditReversal extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'treasury.credit_reversal';

    const NETWORK_ACH = 'ach';
    const NETWORK_STRIPE = 'stripe';

    const STATUS_CANCELED = 'canceled';
    const STATUS_POSTED = 'posted';
    const STATUS_PROCESSING = 'processing';

    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, \Stripe\Collection::class, $params, $opts);
    }

    public static function retrieve($id, $opts = null)
    {
        $opts = \Stripe\Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }
}
