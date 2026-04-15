<?php


namespace Stripe;

class Review extends ApiResource
{
    const OBJECT_NAME = 'review';

    const CLOSED_REASON_ACKNOWLEDGED = 'acknowledged';
    const CLOSED_REASON_APPROVED = 'approved';
    const CLOSED_REASON_CANCELED = 'canceled';
    const CLOSED_REASON_DISPUTED = 'disputed';
    const CLOSED_REASON_PAYMENT_NEVER_SETTLED = 'payment_never_settled';
    const CLOSED_REASON_REDACTED = 'redacted';
    const CLOSED_REASON_REFUNDED = 'refunded';
    const CLOSED_REASON_REFUNDED_AS_FRAUD = 'refunded_as_fraud';

    const OPENED_REASON_MANUAL = 'manual';
    const OPENED_REASON_RULE = 'rule';

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

    const REASON_APPROVED = 'approved';
    const REASON_DISPUTED = 'disputed';
    const REASON_MANUAL = 'manual';
    const REASON_REFUNDED = 'refunded';
    const REASON_REFUNDED_AS_FRAUD = 'refunded_as_fraud';
    const REASON_RULE = 'rule';

    public function approve($params = null, $opts = null)
    {
        $url = $this->instanceUrl() . '/approve';
        list($response, $opts) = $this->_request('post', $url, $params, $opts);
        $this->refreshFrom($response, $opts);

        return $this;
    }
}
