<?php


namespace Stripe;

class Refund extends ApiResource
{
    const OBJECT_NAME = 'refund';

    use ApiOperations\Update;

    const FAILURE_REASON_EXPIRED_OR_CANCELED_CARD = 'expired_or_canceled_card';
    const FAILURE_REASON_LOST_OR_STOLEN_CARD = 'lost_or_stolen_card';
    const FAILURE_REASON_UNKNOWN = 'unknown';

    const PENDING_REASON_CHARGE_PENDING = 'charge_pending';
    const PENDING_REASON_INSUFFICIENT_FUNDS = 'insufficient_funds';
    const PENDING_REASON_PROCESSING = 'processing';

    const REASON_DUPLICATE = 'duplicate';
    const REASON_EXPIRED_UNCAPTURED_CHARGE = 'expired_uncaptured_charge';
    const REASON_FRAUDULENT = 'fraudulent';
    const REASON_REQUESTED_BY_CUSTOMER = 'requested_by_customer';

    const STATUS_CANCELED = 'canceled';
    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';
    const STATUS_REQUIRES_ACTION = 'requires_action';
    const STATUS_SUCCEEDED = 'succeeded';

    public static function create($params = null, $options = null)
    {
        self::_validateParams($params);
        $url = static::classUrl();

        list($response, $opts) = static::_staticRequest('post', $url, $params, $options);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public static function all($params = null, $opts = null)
    {
        $url = static::classUrl();

        return static::_requestPage($url, Collection::class, $params, $opts);
    }

    public static function retrieve($id, $opts = null)
    {
        $opts = Util\RequestOptions::parse($opts);
        $instance = new static($id, $opts);
        $instance->refresh();

        return $instance;
    }

    public static function update($id, $params = null, $opts = null)
    {
        self::_validateParams($params);
        $url = static::resourceUrl($id);

        list($response, $opts) = static::_staticRequest('post', $url, $params, $opts);
        $obj = Util\Util::convertToStripeObject($response->json, $opts);
        $obj->setLastResponse($response);

        return $obj;
    }

    public function cancel($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/cancel';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
