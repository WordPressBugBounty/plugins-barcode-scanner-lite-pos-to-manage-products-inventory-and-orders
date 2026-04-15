<?php


namespace Stripe\Issuing;

class Card extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'issuing.card';

    use \Stripe\ApiOperations\Update;

    const CANCELLATION_REASON_DESIGN_REJECTED = 'design_rejected';
    const CANCELLATION_REASON_LOST = 'lost';
    const CANCELLATION_REASON_STOLEN = 'stolen';

    const REPLACEMENT_REASON_DAMAGED = 'damaged';
    const REPLACEMENT_REASON_EXPIRED = 'expired';
    const REPLACEMENT_REASON_LOST = 'lost';
    const REPLACEMENT_REASON_STOLEN = 'stolen';

    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';
    const STATUS_INACTIVE = 'inactive';

    const TYPE_PHYSICAL = 'physical';
    const TYPE_VIRTUAL = 'virtual';

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
