<?php


namespace Stripe\Billing;

class MeterEventAdjustment extends \Stripe\ApiResource
{
    const OBJECT_NAME = 'billing.meter_event_adjustment';

    const STATUS_COMPLETE = 'complete';
    const STATUS_PENDING = 'pending';

    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = \Stripe\Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }
}
