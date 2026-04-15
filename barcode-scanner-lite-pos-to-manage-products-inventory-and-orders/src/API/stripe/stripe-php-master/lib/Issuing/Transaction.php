<?php


namespace Stripe\Issuing;

class Transaction extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'issuing.transaction';

    use \Stripe\ApiOperations\Update;

    const TYPE_CAPTURE = 'capture';
    const TYPE_REFUND = 'refund';

    const WALLET_APPLE_PAY = 'apple_pay';
    const WALLET_GOOGLE_PAY = 'google_pay';
    const WALLET_SAMSUNG_PAY = 'samsung_pay';

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

    public static function update($id, $params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::resourceUrl($id);

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }
}
