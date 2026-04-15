<?php


namespace Stripe;

class Token extends ApiResource
{
    const OBJECT_NAME = 'token';

    const TYPE_ACCOUNT = 'account';
    const TYPE_BANK_ACCOUNT = 'bank_account';
    const TYPE_CARD = 'card';
    const TYPE_PII = 'pii';

    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public static function retrieve($id, $opts = null)
    {
        $opts = Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }
}
